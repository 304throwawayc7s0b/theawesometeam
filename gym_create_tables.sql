Create Table FitnessMeasurement (
Height double,
Date int,
fmID int,
weight double,
BodyFat int,
Water int,
MuscleMass double,
CustomerID int,
Primary key (fmID, CustomerID),
FOREIGN KEY (CustomerID) REFERENCES GymMember
ON DELETE CASCADE
)

Create Table GymMember (
CustomerID int,
Type char(20),
StartDate int NOT NULL,
EndDate	int NOT NULL,
StartTime int NOT NULL,
EndTime	int NOT NULL,
PRIMARY KEY (CustomerID),
FOREIGN KEY(CustomerID) REFERENCES Customer
ON DELETE CASCADE
FOREIGN KEY (StartDate, EndDate, StartTime, EndTime) REFERENCES TimePeriod
ON UPDATE CASCADE
ON DELETE NO ACTION)

Create Table Customer (
CustomerID int,
Phone# char(50),
Name char(50),
Address	char(1000),
CreditCard# char(50),
Primary key (CustomerID)
)

Create Table Instructor (
InstructorID int,
Phone# char(50),
Name char(50),
Gender char(50),
HrRate double,
FirstName char(50),
LastName char(50),
InstructorType char(50)
Primary key (InstructorID)
)


Create Table Class (
ClassID int,
Duration int,
TotalFee int,
Room # int,
InstructorID int NOT NULL,
StartDate int NOT NULL,
EndDate	int NOT NULL,
StartTime int NOT NULL,
EndTime	int NOT NULL,
ClassTypeID int NOT NULL,
Primary key (ClassID),
UNIQUE	(InstructorID),
FOREIGN KEY(InstructorID) REFERENCES Instructor
ON DELETE CASCADE,
FOREIGN KEY (StartDate, EndDate, StartTime, EndTime) 
REFERENCES TimePeriod
ON DELETE No action
FOREIGN KEY (ClassTypeID) 
REFERENCES ClassType
ON DELETE No action
)

Create Table TimePeriod (
StartDate int,
EndDate	int,
StartTime int,
EndTime	int,
Primary key (StartDate, EndDate, StartTime, EndTime)
)


Create Table ClassType(
ClassTypeID int,
Description char(100),
HrRate int,
Features char(200),
Primary key (ClassTypeID)
)

CREATE TABLE Branch(
Location char(30),
City char(20),
Primary key (Location, City)
)

CREATE TABLE Equipment(
EquipID	int,
PurchaseDate int,
PurchasePrice double,
Type char(15)
Location char(30),
City char(20),
PRIMARY KEY (EquipID, Location, City)
FOREIGN KEY (Location, City) REFERENCES Branch
ON DELETE CASCADE
)

Create Table Hosted(
Location char(30),
City char(30),
ClassID	Integer
PRIMARY KEY(Location, City, ClassID)
FOREIGN KEY(Location, City) References Branch
ON DELETE CASCADE
ON UPDATE CASCADE
FOREIGN KEY	(ClassID) References Class
ON DELETE CASCADE
ON UPDATE CASCADE
)
Create Table Reservation(
Location char(30),
City char(30),
ClassID	int NOT NULL,
CustomerID int NOT NULL,
Confirmation# int,
CreditCard# char(50),
CancelationFee int,
CreatedTime char(20),
CreatedDate char(20),
DiscountCode int,
Primary key (Confirmation#)
FOREIGN KEY(ClassID) References Class
ON DELETE NO ACTION
FOREIGN KEY(CustomerID) References Customer
ON DELETE CASCADE
)

Create Table IsQualifiledIn(
InstructorID int NOT NULL,
ClassTypeID int NOT NULL,
Primary key (InstructorID, ClassTypeID)
FOREIGN KEY(InstructorID) References Instructor
ON DELETE CASCADE
ON UPDATE CASCADE
FOREIGN KEY(ClassTypeId) References ClassType
ON DELETE NO ACTION
ON UPDATE CASCADE
)

