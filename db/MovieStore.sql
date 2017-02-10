/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2/02/2017 09:00:19                           */
/*==============================================================*/


drop table if exists GENRE;

drop table if exists MOVIE;

drop table if exists RENTAL;

drop table if exists USER;

/*==============================================================*/
/* Table: GENRE                                                 */
/*==============================================================*/
create table GENRE
(
   GENRE_ID             int not null,
   GENRE_NAME           varchar(30) not null,
   primary key (GENRE_ID)
);

/*==============================================================*/
/* Table: MOVIE                                                 */
/*==============================================================*/
create table MOVIE
(
   MOVIE_ID             int not null,
   GENRE_ID             int not null,
   MOVIE_NAME           varchar(30) not null,
   MOVIE_DESC           varchar(150) not null,
   primary key (MOVIE_ID)
);

/*==============================================================*/
/* Table: RENTAL                                                */
/*==============================================================*/
create table RENTAL
(
   USER_ID              int not null,
   MOVIE_ID             int not null,
   RENTAL_ID            int not null,
   RENTAL_INITDATE      date not null,
   RENTAL_ENDDATE       date not null,
   RENTAL_STATUS        varchar(30) not null,
   primary key (USER_ID, MOVIE_ID)
);

/*==============================================================*/
/* Table: USER                                                  */
/*==============================================================*/
create table USER
(
   USER_ID              int not null,
   USER_NAME            varchar(30) not null,
   USER_LASTNAME        varchar(30) not null,
   USER_LOGIN           varchar(30) not null,
   USER_PASSWORD        varchar(80) not null,
   USER_TYPE            varchar(10) not null,
   primary key (USER_ID)
);

alter table MOVIE add constraint FK_HAS foreign key (GENRE_ID)
      references GENRE (GENRE_ID) on delete restrict on update restrict;

alter table RENTAL add constraint FK_RENTAL foreign key (USER_ID)
      references USER (USER_ID) on delete restrict on update restrict;

alter table RENTAL add constraint FK_RENTAL2 foreign key (MOVIE_ID)
      references MOVIE (MOVIE_ID) on delete restrict on update restrict;

