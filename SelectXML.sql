SELECT d.stationid, m.`name`,COUNT(*) as 'airboxs', m.address, m.lat, m.lng, m.type, pm25.pm25_1,pm25.pm25_5, pm25.pm25_10
FROM distance d, markers m, station s
LEFT JOIN
(SELECT A1.sid,pm25_1,pm25_5,pm25_10
FROM
(SELECT a.id as aid, s.ID as sid, AVG(a.pm25) as pm25_1
FROM airbox as a, station as s,distance as d
WHERE s.ID = d.stationid AND a.id = d.airboxid AND d.`level` <=1
GROUP BY s.ID) as A1,
(SELECT a.id as aid, s.ID as sid, AVG(a.pm25) as pm25_5
FROM airbox as a, station as s,distance as d
WHERE s.ID = d.stationid AND a.id = d.airboxid AND d.`level` <=5
GROUP BY s.ID) as A5,
(SELECT a.id as aid, s.ID as sid, AVG(a.pm25) as pm25_10
FROM airbox as a, station as s,distance as d
WHERE s.ID = d.stationid AND a.id = d.airboxid AND d.`level` <=10
GROUP BY s.ID) as A10
WHERE A1.sid = A5.sid AND A5.sid = A10.sid) as pm25
ON  pm25.sid = s.ID
WHERE s.ID = d.stationid AND m.`name` = s.StationName 
GROUP BY d.stationid