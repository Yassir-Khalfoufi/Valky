create database if not exists cinema_db;
use cinema_db;

create table users (
  id int auto_increment primary key,
  username varchar(50) unique not null,
  email varchar(100) unique not null,
  password varchar(255) not null,
  created_at timestamp default current_timestamp
);

create table movies (
  id int auto_increment primary key,
  title varchar(255) not null,
  year int,
  director varchar(255),
  genre varchar(100),
  description text,
  created_at timestamp default current_timestamp
);

create table user_movies (
  id int auto_increment primary key,
  user_id int not null,
  movie_id int not null,
  status enum('watchlist','watched') default 'watchlist',
  rating tinyint check (rating between 1 and 5),
  added_at timestamp default current_timestamp,
  unique key unique_user_movie (user_id, movie_id),
  foreign key (user_id) references users(id) on delete cascade,
  foreign key (movie_id) references movies(id) on delete cascade
);

create table reviews (
  id int auto_increment primary key,
  user_id int not null,
  movie_id int not null,
  body text not null,
  created_at timestamp default current_timestamp,
  foreign key (user_id) references users(id) on delete cascade,
  foreign key (movie_id) references movies(id) on delete cascade
);

create table lists (
  id int auto_increment primary key,
  user_id int not null,
  name varchar(255) not null,
  description text,
  created_at timestamp default current_timestamp,
  foreign key (user_id) references users(id) on delete cascade
);

create table list_movies (
  list_id int not null,
  movie_id int not null,
  primary key (list_id, movie_id),
  foreign key (list_id) references lists(id) on delete cascade,
  foreign key (movie_id) references movies(id) on delete cascade
);

insert into movies (title, year, director, genre, description) values
('The Godfather', 1972, 'Francis Ford Coppola', 'Crime', 'The aging patriarch of an organized crime dynasty transfers control to his reluctant son.'),
('Blade Runner 2049', 2017, 'Denis Villeneuve', 'Sci-Fi', 'A new blade runner unearths a secret that threatens to destabilize society.'),
('Parasite', 2019, 'Bong Joon-ho', 'Thriller', 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.'),
('Dune', 2021, 'Denis Villeneuve', 'Sci-Fi', 'A noble family becomes embroiled in a war for control over the galaxy''s most valuable asset.'),
('The Dark Knight', 2008, 'Christopher Nolan', 'Action', 'Batman faces the Joker, a criminal mastermind who wants to plunge Gotham into anarchy.');
