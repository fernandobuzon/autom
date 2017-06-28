CREATE TABLE "main"."faces"
(
"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE ,
"name" TEXT NOT NULL ,
"img" BLOB NOT NULL
);

CREATE TABLE "main"."doors"
(
"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE ,
"name" TEXT NOT NULL UNIQUE
);

CREATE TABLE "main"."doors_faces"
(
"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE ,
"door_id" INTEGER NOT NULL ,
"face_id" INTEGER NOT NULL ,
FOREIGN KEY(door_id) REFERENCES doors(id) ON DELETE CASCADE, 
FOREIGN KEY(face_id) REFERENCES faces(id) ON DELETE CASCADE
);

CREATE TABLE "main"."cameras"
(
"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE ,
"door_id" INTEGER DEFAULT NULL,
"name" TEXT NOT NULL ,
"netcam_url" TEXT NOT NULL ,
"netcam_userpass" TEXT ,
"v4l2_palette" INT NOT NULL DEFAULT 17 ,
"norm" INT NOT NULL DEFAULT 0 ,
"width" INT NOT NULL DEFAULT 320 ,
"height" INT NOT NULL DEFAULT 240 ,
"framerate" INT NOT NULL DEFAULT 2 ,
"minimum_frame_time" INT NOT NULL DEFAULT 1 ,
"netcam_keepalive" BOOL NOT NULL DEFAULT 1,
"auto_brightness" BOOL NOT NULL DEFAULT 0 ,
"brightness" INT NOT NULL DEFAULT 0,
"contrast" INT NOT NULL DEFAULT 0 ,
"saturation" INT NOT NULL DEFAULT 0 ,
"hue" INT NOT NULL DEFAULT 0 ,
FOREIGN KEY(door_id) REFERENCES doors(id) ON DELETE CASCADE
);

CREATE TABLE "main"."logs"
(
"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE ,
"timestamp" DATETIME NOT NULL DEFAULT (DATETIME(CURRENT_TIMESTAMP, 'LOCALTIME')) ,
"camera_id" INTEGER NOT NULL ,
"door_id" INTEGER NOT NULL ,
"face_id" INTEGER NOT NULL ,
"img" BLOB NOT NULL ,
"match" FLOAT NOT NULL ,
FOREIGN KEY(camera_id) REFERENCES cameras(id) ON DELETE CASCADE ,
FOREIGN KEY(door_id) REFERENCES doors(id) ON DELETE CASCADE , 
FOREIGN KEY(face_id) REFERENCES faces(id) ON DELETE CASCADE
);

CREATE TABLE "main"."settings"
(
"setting" TEXT PRIMARY KEY NOT NULL UNIQUE ,
"value" TEXT DEFAULT NULL
);

CREATE TABLE "main"."users"
(
"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE ,
"name" TEXT NOT NULL ,
"login" TEXT NOT NULL UNIQUE ,
"passwd" TEXT NOT NULL
);

insert into settings ('setting','value') values ('conf_path','/etc/motion');
insert into settings ('setting','value') values ('gallery_path','/etc/motion/gallery');
insert into settings ('setting','value') values ('br_bin','/usr/local/bin/br');
insert into settings ('setting','value') values ('restart_cmd','sudo /etc/init.d/motion restart');
insert into settings ('setting','value') values ('start_cmd','sudo /etc/init.d/motion start');
insert into settings ('setting','value') values ('stop_cmd','sudo /etc/init.d/motion stop');
insert into settings ('setting','value') values ('check_cmd','pgrep motion');
insert into settings ('setting','value') values ('match','2');
insert into settings ('setting','value') values ('interval','6');
insert into settings ('setting','value') values ('log','/var/log/br.log');
insert into settings ('setting','value') values ('csv','/tmp/out.csv');
insert into settings ('setting','value') values ('assessment_tmp','/tmp/assessment.tmp');
insert into SQLITE_SEQUENCE ('name','seq') values ('faces',0);
