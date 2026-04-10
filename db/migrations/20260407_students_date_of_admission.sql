-- Matches u921830511_miqt_new (2).sql — run if your `students` table is missing this column.
ALTER TABLE `students` ADD COLUMN `date_of_admission` date DEFAULT NULL AFTER `mother_name`;
