#!/usr/bin/env python
import urllib2
import json
import sys
import mysql.connector
from mysql.connector import errorcode
reload(sys)
sys.setdefaultencoding('utf8')
#sql connection
config = {
  'user': 'root',
  'password': 'ckklab2506',
  'host': '163.17.136.150',
  'database': '1062xml',  
}
try:
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()

    with open('station.json' , 'r') as reader:
        jf = json.loads(reader.read())

    add_station = ("INSERT INTO station "
                    "(ID, TimeID, StationName, lat, lng, branch, address, tel) "
                    "VALUES (%s, %s, %s, %s, %s, %s, %s, %s)")
    for value in jf["features"]:
        detail = value['properties']
        #print detail
        print detail['ID']+":"+str(detail['StationName'])
        Lat = detail['Lat']
        Lng = detail['Lng']
        #update to db
        data_station = (str(detail['ID']), str(detail['TimeID']), str(detail['StationName']), Lat, Lng, str(detail['Branch']), str(detail['Address']), str(detail['Tel']))
        # Insert new employee
        cursor.execute(add_station, data_station)
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
  print("finish")
  cnx.close()