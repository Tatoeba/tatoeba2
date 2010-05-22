-- this way contributions will finally have a real id field
ALTER TABLE contributions ADD id_temps INT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id_temps);
ALTER TABLE contributions DROP COLUMN contributions.id ;
ALTER TABLE contributions change id_temps id  INT UNSIGNED NOT NULL AUTO_INCREMENT ;
