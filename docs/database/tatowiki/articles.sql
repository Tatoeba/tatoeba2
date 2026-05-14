-- copied over from tatowiki schema, to help schema creation in unit tests
-- source: https://github.com/Tatoeba/tatowiki/blob/master/app/sql/sqlite3.sql

create table articles (
    id integer primary key autoincrement not null,
    group_id  integer   not null default 0,
    lang text not null,
    slug text not null,
    title text not null,
    content text not null,
    locked boolean default false not null,
    unique (lang,slug)
);
