ALTER TABLE `simapes`.`md_siswa` 
ADD COLUMN `KIP_SISWA` VARCHAR(10) NULL DEFAULT NULL AFTER `CITA_SISWA`;

UPDATE `simapes`.`md_pengaturan` SET `NAMA_PENGATURAN` = '{\"2\":[2,3],\"3\":[1,2],\"4\":[1],\"5\":[2],\"6\":[2]}' WHERE (`ID_PENGATURAN` = 'psb_ujian');
