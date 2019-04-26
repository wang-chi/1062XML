#!/usr/bin/env python

import urllib2
import json
import sys
import mysql.connector
from mysql.connector import errorcode
#sql connection
config = {
    'user': 'root',
    'password': 'ho860604',
    'host': 'localhost',
    'database': '1062xml',
}


#read data from url
url = "https://pm25.lass-net.org/data/last-all-airbox.json"
req = urllib2.Request(url)
opener = urllib2.build_opener()
f = opener.open(req)
json = json.loads(f.read())
#print json
for value in json["feeds"]:
    adevice = value["SiteName"]
    ano = value["device_id"]
    print "No." + ano + " " + adevice
    humidity = value['s_h0']
    temperature = value['s_t0']
    pm25 = value['s_d0']
    date = value['date']
    time = value['time']
    timestamp = value['timestamp']
    data_airbox = (ano, pm25, temperature, humidity, date, time,timestamp)
    print data_airbox
    try:
        cnx = mysql.connector.connect(**config)
        cursor = cnx.cursor()
        print "db connecte successful"

        add_airbox = ("INSERT INTO airbox "
                      "(id, pm25, temperature, humidity, date, time, timestamp) "
                      "VALUES (%s, %s, %s, %s, %s, %s, %s)")

        # Insert new employee
        cursor.execute(add_airbox, data_airbox)
        a_no = cursor.lastrowid
        # Make sure data is committed to the database
        cnx.commit()
        cursor.close()
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cnx.close()
