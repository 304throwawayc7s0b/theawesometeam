Create Table TimePeriod (
StartDate varchar2(8),
EndDate	varchar2(8),
StartTime varchar2(8),
EndTime	varchar2(8),
Primary key (StartDate, EndDate, StartTime, EndTime)
);

Create Table ClassType(
ClassTypeID integer,
Description varchar2(100),
HrRate varchar2(8),
Features varchar2(200),
Primary key (ClassTypeID)
);

Create Table Instructor (
InstructorID integer,
Phone varchar2(50),
Gender varchar2(50),
HrRate BINARY_FLOAT,
FirstName varchar2(50),
LastName varchar2(50),
InstructorType varchar2(50),
Primary key (InstructorID)
);

Create Table Class (
ClassID integer,
Duration varchar2(8),
TotalFee varchar2(8),
Room varchar2(8),
InstructorID integer NOT NULL,
StartDate varchar2(8) NOT NULL,
EndDate	varchar2(8) NOT NULL,
StartTime varchar2(8) NOT NULL,
EndTime	varchar2(8) NOT NULL,
ClassTypeID integer NOT NULL,
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

Create Table Customer (
CustomerID integer,
Phone varchar2(50),
Name varchar2(50),
Address	varchar2(1000),
CreditCard varchar2(50),
Primary key (CustomerID)
);

CREATE TABLE GymBranch(
Location varchar2(100),
City varchar2(50),
Primary key (Location, City)
);

Create Table GymMember (
CustomerID integer,
custType varchar2(20),
StartDate varchar2(8) NOT NULL,
EndDate	varchar2(8) NOT NULL,
StartTime varchar2(8) NOT NULL,
EndTime	varchar2(8) NOT NULL,
PRIMARY KEY (CustomerID),
FOREIGN KEY(CustomerID) REFERENCES Customer
ON DELETE CASCADE,
FOREIGN KEY (StartDate, EndDate, StartTime, EndTime) REFERENCES TimePeriod
ON DELETE SET NULL);

Create Table FitnessMeasurement (
Height BINARY_FLOAT,
startDate varchar2(16),
fmID integer,
weight BINARY_FLOAT,
BodyFat varchar2(8),
Water varchar2(8),
MuscleMass BINARY_FLOAT,
CustomerID integer,
Primary key (fmID, CustomerID),
FOREIGN KEY (CustomerID) REFERENCES GymMember
ON DELETE CASCADE
);

CREATE TABLE Equipment(
EquipID	integer,
PurchaseDate varchar2(16),
PurchasePrice varchar2(8),
EquipType varchar2(25),
Location varchar2(30),
City varchar2(20),
PRIMARY KEY (EquipID),
FOREIGN KEY (Location, City) REFERENCES GymBranch
ON DELETE CASCADE
);

Create Table IsQualifiledIn(
InstructorID integer NOT NULL,
ClassTypeID integer NOT NULL,
Primary key (InstructorID, ClassTypeID),
FOREIGN KEY(InstructorID) References Instructor
ON DELETE CASCADE,
FOREIGN KEY(ClassTypeId) References ClassType
ON DELETE SET NULL);

Create Table Hosted(
Location varchar2(30),
City varchar2(30),
ClassID	integer,
PRIMARY KEY(Location, City, ClassID),
FOREIGN KEY(Location, City) References GymBranch
ON DELETE CASCADE,
FOREIGN KEY	(ClassID) References Class
ON DELETE CASCADE
);

Create Table Reservation(
Location varchar2(30),
City varchar2(30),
ClassID	integer NOT NULL,
CustomerID integer NOT NULL,
Confirmation varchar2(8),
CreditCard varchar2(50),
CancelationFee varchar2(8),
CreatedTime varchar2(20),
CreatedDate varchar2(20),
DiscountCode varchar2(8),
Primary key (Confirmation),
FOREIGN KEY(ClassID) References Class,
FOREIGN KEY(CustomerID) References Customer
ON DELETE CASCADE
);

