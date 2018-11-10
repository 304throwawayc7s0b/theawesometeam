

create table fitness_measurement 
(	height 		integer,
	taken_date	date not null,
	fmID		integer not null,
	weight		integer,
	body_fat 	integer,
	water 		integer,
	muscle_mass	integer,
	customerID	integer not null);

commit;
