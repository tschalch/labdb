ALTER TABLE inventory ADD COLUMN billed DATE DEFAULT 0;
ALTER TABLE inventory ADD COLUMN www TEXT AFTER description;
UPDATE inventory SET billed='2012-01-01';
ALTER TABLE inventory ADD COLUMN poNumber VARCHAR(255);
