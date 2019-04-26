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

cnx = mysql.connector.connect(**config)
cursor = cnx.cursor()
query = ("SELECT DISTINCT airboxid FROM airboxlist")
airboxlist = []
try:
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()
    print "db connect success"
    cursor.execute(query)
    for(airboxid) in cursor:
     airboxlist.append(airboxid[0])
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

#read parameter
deviceId = ''
for i in range(len(airboxlist)):
  deviceId = airboxlist[i]
  #read data from url
  url = "https://pm25.lass-net.org/data/last.php?device_id=" + str(deviceId)
  print "[" + str(i) + "]:" + url
  req = urllib2.Request(url)
  opener = urllib2.build_opener()
  f = opener.open(req)
  data = f.read()
  print "type(data):" + str(type(data))
  print "type(json):" + str(type(json))

  if (isinstance(data, str)):
    json = json.loads(data)
  elif (isinstance(data, dict)):
    json = json.load(data)
  #print isinstance(data, (str,dict))
  #json = json.loads(data)
  for value in json["feeds"]:
      airbox = value["AirBox"]
      print "PM25:" + str(airbox['s_d0'])
      print "Temperature:" + str(airbox['s_t0'])
      print "Humidity:" + str(airbox['s_h0'])
      print "time:" + airbox['time']
      print "date:" + airbox['date']
