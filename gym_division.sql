//Find id, first name and last name of instructors qualified to teach all class types

SELECT  instructorid, firstname, lastname FROM Instructor q1
WHERE NOT EXISTS 
(( SELECT ct.ClassTypeID FROM ClassType ct )
 MINUS 
 (SELECT q2.ClassTypeID FROM IsQualifiledIn q2 WHERE q2.InstructorID = q1.InstructorID));
