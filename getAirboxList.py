#!/usr/bin/env python
import urllib2
import json
import sys
import mysql.connector
from mysql.connector import errorcode

#sql connection
config = {
    'user': 'root',
    'password': 'ckklab2506',
    'host': '163.17.136.150',
    'database': '1062XML',
}
#read data from url
url = "https://pm25.lass-net.org/data/last-all-airbox.json"
req = urllib2.Request(url)
opener = urllib2.build_opener()
f = opener.open(req)
json = json.loads(f.read())
#print json
try:
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()
    print "db connect success"
    for value in json["feeds"]:
        #print "No." + ano + " " + adevice
        #print value
        for key in value:
            #print str(key) + " = " + str(value[key])
            if key == "device_id":
                deviceId = value[key]
            if key == "gps_lat":
                gps_lat = value[key]
            if key == "timestamp":
                timestamp = value[key]
            if key == "gps_lon":
                gps_lon = value[key]
        print "device_id = " + deviceId
        data_airbox = (deviceId, gps_lat, gps_lon, timestamp)
        add_airbox = ("INSERT INTO airboxlist "
                        "(airboxid, lat, lng, timestamp) "
                        "VALUES (%s, %s, %s, %s)")
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

