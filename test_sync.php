SELECT emailId FROM `user` WHERE emailId IN (SELECT emailId from `test`)
Showing rows 0 - 24 (1928158 total, Query took 22.0208 seconds.)


SELECT count(userId) FROM `user` WHERE emailId IN (SELECT emailId from `test`)
Showing rows 0 - 0 (1 total, Query took 105.1241 seconds.)


SELECT u.emailId FROM user as u  
JOIN test as t
ON u.emailId = t.emailId
WHERE u.emailId = t.emailId
Showing rows 0 - 24 (1928158 total, Query took 0.0006 seconds.)


SELECT u.emailId FROM user as u JOIN test as t ON u.emailId = t.emailId
Showing rows 0 - 24 (1928158 total, Query took 0.0010 seconds.)


SELECT u.emailId FROM user u join test t ON u.emailId = t.emailId where u.emailId = t.emailId limit 100
Showing rows 0 - 99 (100 total, Query took 0.0885 seconds.)

SELECT u.emailId FROM user u join test t where u.emailId = t.emailId limit 100
Showing rows 0 - 99 (100 total, Query took 0.0859 seconds.)


	test - 798,436
	user - 4,403,727

	1
	10
	100  - 4403616
	1000 - 4402616 - 4.2523
	5000 - 4397616 - 172.4495 
	1  	 - 4397615 - 0.0076
	1500 - 4396115 - 100.2859
	1000 - 4395115 - 79.5153
	1 - 4395114 - 0.0089
	10 - 0.7718
	100 - 8.3916
	100 - 8.4404 - orderby
	200 - 17.6152 - orderby
	200 - 17.7622


	DELETE from user WHERE emailId IN (SELECT emailId from test) limit 5000



	select emailId from user WHERE (emailId,phone) In (select emailId,phone from test) LIMIT 1
	seab@piqqik.gl

	select emailId from user WHERE emailId In (select emailId from test) LIMIT 1
	seab@piqqik.gl

	select emailId from user WHERE phone In (select phone from test) LIMIT 1
	seab@piqqik.gl


	=================================
	SELECT count(emailId) FROM `user` WHERE emailId IN (SELECT emailId from unsubscriber WHERE emailId != '' ORDER BY emailId) ORDER BY emailId LIMIT 500

	33249 = 3.5412
	31617 + 1633 

	SELECT count(emailId) FROM `user` WHERE emailId IN (SELECT emailId from unsubscriber ORDER BY emailId) ORDER BY emailId LIMIT 500
	133775 = 3.5172

	132142


	email blank = 100525


	==================================================

	SELECT count(phone) from user WHERE phone IN (SELECT phone from unsubscriber)
	296257

	SELECT count(phone) from user WHERE phone IN (SELECT phone from unsubscriber WHERE phone != '')
	65552


	SELECT count(phone) from user WHERE phone = ''
	230705

	SELECT count(phone) from unsubscriber WHERE phone = ''
	80333


	DELETE FROM user WHERE emailId IN (SELECT emailId from unsubscriber) LIMIT 1 - 1.8065
	DELETE FROM user WHERE emailId IN (SELECT emailId from unsubscriber) LIMIT 10 - 1.7969
	DELETE FROM user WHERE emailId IN (SELECT emailId from unsubscriber WHERE emailId != '') LIMIT 100 - 1.8327


	DELETE FROM user WHERE phone IN (SELECT phone from unsubscriber) LIMIT 1 - arround 500 sec - before index
	DELETE FROM user WHERE phone IN (SELECT phone from unsubscriber) LIMIT 1 - 0.0081 - after indexed
	DELETE FROM user WHERE phone IN (SELECT phone from unsubscriber WHERE phone != '' ) LIMIT 1 - 0.0068
	DELETE FROM user WHERE phone IN (SELECT phone from unsubscriber WHERE phone != '' ) LIMIT 100 -  0.1468


