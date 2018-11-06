Create Table FitnessMeasurement (
Height BINARY_FLOAT,
startDate varchar2(8),
fmID varchar2(8),
weight BINARY_FLOAT,
BodyFat varchar2(8),
Water varchar2(8),
MuscleMass BINARY_FLOAT,
CustomerID varchar2(8),
Primary key (fmID, CustomerID),
FOREIGN KEY (CustomerID) REFERENCES GymMember
ON DELETE CASCADE
);

Create Table GymMember (
CustomerID varchar2(8),
custType char(20),
StartDate varchar2(8) NOT NULL,
EndDate	varchar2(8) NOT NULL,
StartTime varchar2(8) NOT NULL,
EndTime	varchar2(8) NOT NULL,
PRIMARY KEY (CustomerID),
FOREIGN KEY(CustomerID) REFERENCES Customer
ON DELETE CASCADE,
FOREIGN KEY (StartDate, EndDate, StartTime, EndTime) REFERENCES TimePeriod
ON DELETE SET NULL);

Create Table Customer (
CustomerID varchar2(8),
Phone char(50),
Name char(50),
Address	char(1000),
CreditCard char(50),
Primary key (CustomerID)
);

Create Table Instructor (
InstructorID varchar2(8),
Phone char(50),
Name char(50),
Gender char(50),
HrRate BINARY_FLOAT,
FirstName char(50),
LastName char(50),
InstructorType char(50),
Primary key (InstructorID)
);


Create Table Class (
ClassID varchar2(8),
Duration varchar2(8),
TotalFee varchar2(8),
Room varchar2(8),
InstructorID varchar2(8) NOT NULL,
StartDate varchar2(8) NOT NULL,
EndDate	varchar2(8) NOT NULL,
StartTime varchar2(8) NOT NULL,
EndTime	varchar2(8) NOT NULL,
ClassTypeID varchar2(8) NOT NULL,
Primary key (ClassID),
UNIQUE	(InstructorID),
FOREIGN KEY(InstructorID) REFERENCES Instructor
ON DELETE CASCADE,
FOREIGN KEY (StartDate, EndDate, StartTime, EndTime) 
REFERENCES TimePeriod
ON DELETE SET NULL,
FOREIGN KEY (ClassTypeID) 
REFERENCES ClassType
ON DELETE SET NULL
);

Create Table TimePeriod (
StartDate varchar2(8),
EndDate	varchar2(8),
StartTime varchar2(8),
EndTime	varchar2(8),
Primary key (StartDate, EndDate, StartTime, EndTime)
);


Create Table ClassType(
ClassTypeID varchar2(8),
Description char(100),
HrRate varchar2(8),
Features char(200),
Primary key (ClassTypeID)
);

CREATE TABLE GymBranch(
Location char(30),
City char(20),
Primary key (Location, City)
);

CREATE TABLE Equipment(
EquipID	varchar2(8),
PurchaseDate varchar2(8),
PurchasePrice BINARY_FLOAT,
EquipType char(15),
Location char(30),
City char(20),
PRIMARY KEY (EquipID, Location, City),
FOREIGN KEY (Location, City) REFERENCES GymBranch
ON DELETE CASCADE
);

Create Table Hosted(
Location char(30),
City char(30),
ClassID	varchar2(8),
PRIMARY KEY(Location, City, ClassID),
FOREIGN KEY(Location, City) References GymBranch
ON DELETE CASCADE,
FOREIGN KEY	(ClassID) References Class
ON DELETE CASCADE
);
Create Table Reservation(
Location char(30),
City char(30),
ClassID	varchar2(8) NOT NULL,
CustomerID varchar2(8) NOT NULL,
Confirmation varchar2(8),
CreditCard char(50),
CancelationFee varchar2(8),
CreatedTime char(20),
CreatedDate char(20),
DiscountCode varchar2(8),
Primary key (Confirmation),
FOREIGN KEY(ClassID) References Class,
FOREIGN KEY(CustomerID) References Customer
ON DELETE CASCADE
);

Create Table IsQualifiledIn(
InstructorID varchar2(8) NOT NULL,
ClassTypeID varchar2(8) NOT NULL,
Primary key (InstructorID, ClassTypeID),
FOREIGN KEY(InstructorID) References Instructor
ON DELETE CASCADE,
FOREIGN KEY(ClassTypeId) References ClassType
ON DELETE SET NULL);