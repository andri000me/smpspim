
ALTER TABLE `md_hari` ADD `DAY_HARI` VARCHAR(25) NOT NULL AFTER `LIBUR_HARI`;
UPDATE `md_hari` SET `DAY_HARI`='Friday' WHERE `ID_HARI`='1';
UPDATE `md_hari` SET `DAY_HARI`='Saturday' WHERE `ID_HARI`='2';
UPDATE `md_hari` SET `DAY_HARI`='Sunday' WHERE `ID_HARI`='3';
UPDATE `md_hari` SET `DAY_HARI`='Monday' WHERE `ID_HARI`='4';
UPDATE `md_hari` SET `DAY_HARI`='Tuesday' WHERE `ID_HARI`='5';
UPDATE `md_hari` SET `DAY_HARI`='Thursday' WHERE `ID_HARI`='7';
UPDATE `md_hari` SET `DAY_HARI`='Wednesday' WHERE `ID_HARI`='6';
