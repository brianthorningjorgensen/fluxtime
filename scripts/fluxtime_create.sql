create table systemaccount (
	accountid serial primary key,
	customer character varying(100) not null,
        customerid character varying(50),
        active integer not null,
        state integer not null,
	description text,
	unique(customer)
);

create table usergroup (
	id serial primary key,
	permissiongroup character varying(30) not null,
	unique(permissiongroup)
);

create table urlResource (
	id serial 		primary key,
	urlresource 	character varying(40) not null,
	unique(urlresource)
);

create table resourcepermission (
    id serial primary key,
	fkUsergroup				integer	not null,
	fkUrlresource 			integer not null,
	foreign key(fkUsergroup) references usergroup(id),	
	foreign key(fkUrlresource) references urlResource(id)
);

create table client (
	clientid serial primary key,
	cvrid character varying(30),
	clientname character varying(100) not null,
	phone character varying(20),
	email character varying(50),
	street character varying(50),
	house_number character varying(10),
	city character varying(50),
	zip_code character varying(10),
	country character varying(50),
	state integer not null,
        fkaccountid integer not null,
        unique(clientname, fkaccountid),	
	foreign key(fkaccountid) references systemaccount(accountid)	
);

create table contact (
	contactid serial primary key,
	firstname character varying(40) not null,
	lastname character varying(40) not null,
	phone character varying(20) not null,
	email character varying(50) not null,
	description text,
	state integer not null,
        fkaccountid integer not null,
	fkclientid integer not null,	
	foreign key(fkaccountid) references systemaccount(accountid),
	foreign key(fkclientid) references client(clientid)	
);

create table accountclient (
	accountclientid serial primary key,
        fkaccountid integer not null,
	fkclientid integer not null,
	foreign key(fkclientid) references client(clientid),	
	foreign key(fkaccountid) references systemaccount(accountid)	
);

create table flux_user (
	id serial primary key,
	employee_id character varying(30),
	firstname character varying(40) not null,
	lastname character varying(40) not null,
	phone character varying(20),
	private_email character varying(50),
	work_email character varying(50) not null,
        username	character varying(20) 	not null,
	password	character varying(72) not null,
	street character varying(50),
	house_number character varying(10),
	city character varying(50),
	zip_code character varying(10),
	country character varying(50),
	phone_private character varying(20),
	state integer not null,
	fkUserrole integer not null,
        fkaccountid integer not null,
	pivotaltrackerapi character varying(255),
        unique(username, fkaccountid),
        unique(work_email),	
        foreign key(fkUserrole) references usergroup(id),
	foreign key(fkaccountid) references systemaccount(accountid)	
);

create table emailtype (
	id serial primary key,
	emailtype 	character varying(50) not null,
        unique(emailtype)
);

create table email (
	id serial primary key,
	userFk 		integer not null,
	senttime	timestamp not null,
	emailtypeFk	integer not null,
        fkaccountid       integer not null,
	foreign key(userfk) references flux_user(id),
	foreign key(emailtypeFk) references emailtype(id),
        foreign key(fkaccountid) references systemaccount(accountid)
);

create table project (
	projectid serial primary key,
	projectname character varying(50) not null,
	createdate DATE not null,
	state integer not null,
	secondid character varying(30),
	fkclientid integer,
         fk_projectmanager integer,
        active integer not null,
 	fkaccountid integer not null,
	foreign key (fk_projectmanager) references flux_user(id),
	foreign key(fkaccountid) references systemaccount(accountid),
	foreign key (fkclientid) references client(clientid),
	unique(projectname, fkaccountid)
);
 
create table projectlabel (
	labelid serial primary key,
        secondid character varying(30),
	labelname character varying(50) not null,
	state integer not null,
	fk_projectid integer not null,
	 fkaccountid       integer not null,
	foreign key (fk_projectid) references project(projectid),
	foreign key(fkaccountid) references systemaccount(accountid),
	unique(fk_projectid, labelname, fkaccountid)
);


create table projectuser (
	projectuserid serial primary key,
	fk_userid integer not null,
	fk_projectid integer not null,
	 fkaccountid       integer not null,
	foreign key (fk_projectid) references project(projectid),
         foreign key (fk_userid) references flux_user(id),
	foreign key(fkaccountid) references systemaccount(accountid),
	unique(fk_userid, fk_projectid)
);

create table projectcontact (
	projectcontactid serial primary key,
	fkprojectid integer not null,
	fkcontactid integer not null,
	fkaccountid       integer not null,
	foreign key (fkprojectid) references project(projectid),
         foreign key (fkcontactid) references contact(contactid),
	foreign key(fkaccountid) references systemaccount(accountid),
	unique(fkcontactid, fkprojectid)
);

create table task (
	taskid serial primary key,
	secondid character varying(30),
	taskname text not null,
    fkProjectid integer not null,
	fkLabelid integer,
	state integer not null,
	fkCreator integer not null,
	status character varying(20) not null,
	points character varying(30),
	taskType character varying(30),
	description text,
	 fkaccountid       integer not null,
     foreign key(fkCreator) references flux_user(id),
    foreign key(fkProjectid) references project(projectid),		
	 foreign key(fkLabelid) references projectlabel(labelid),
foreign key(fkaccountid) references systemaccount(accountid)

);

create table taskowner (
    taskownerId serial primary key,
    fkUserid integer not null,
	fkTaskid integer not null,
	 fkaccountid       integer not null,
	foreign key(fkUserid) references flux_user(id),		
	foreign key(fkTaskid) references task(taskid),
	foreign key(fkaccountid) references systemaccount(accountid),
	unique(fkTaskid, fkUserid)
);

create table timereg (
	timeregId serial primary key,
    fkTaskownerid integer not null,
	timeStart TIMESTAMP not null,
	timeStop TIMESTAMP,
	state integer not null, 
	 fkaccountid       integer not null,
    foreign key(fkTaskownerid) references taskowner(taskownerId),
	foreign key(fkaccountid) references systemaccount(accountid),
	unique(fkTaskownerid, timeStart)
);

