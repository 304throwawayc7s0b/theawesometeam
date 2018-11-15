--Data for table  Customer
insert into customer values (1,'778-319-3333','Sarina','15168 32nd Street', '1111-111-1111');



--Data for table  GymMember
insert into gymmember values (1,'monthly member', '20181001','20181101', '11am', '12pm');


--Data for table FitnessMeasurement


--Data for table Instructor
insert into instructor values (1,'778-778-778','female',30,'Ann', 'Perkins', 'Yoga');
insert into instructor values (2,'778-123-778','male',20,'Kev', 'Perkins', 'Yoga');

--Data for table TimePeriod
insert into timeperiod values('20181001', '20181101', '11am', '12pm');

--Data for table ClassType
insert into classtype values(1, 'beginner yoga', '30', 'yoga');

--Data for table Class
insert into class values(1,'1hr','45','Room10',1,'20181001','20181101', '11am', '12pm',1);

--Data for table Equipment


--Data for table Hosted


--Data for table Reservation
insert into reservation values('Point Grey','Vancouver',1,1,'12345678','1111-111-1111','$30','12:30pm','2018-10-01','n/a');

--Data for table IsQualifiledIn
  
 --Data for table GymBranch
 insert into GymBranch values('Sunset Centre','Vancouver');
 insert into GymBranch values('Killarney Centre','Vancouver');
 
commit;
