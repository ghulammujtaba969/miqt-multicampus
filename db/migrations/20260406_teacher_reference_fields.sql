-- Teacher reference / history (ported from 02-04-2026)
ALTER TABLE teachers ADD COLUMN reference_name VARCHAR(100) DEFAULT NULL AFTER photo;
ALTER TABLE teachers ADD COLUMN reference_number VARCHAR(50) DEFAULT NULL AFTER reference_name;
ALTER TABLE teachers ADD COLUMN past_history TEXT DEFAULT NULL AFTER reference_number;
