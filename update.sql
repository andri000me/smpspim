ALTER TABLE `simapes`.`bk_pemanggilan` 
DROP FOREIGN KEY `bk_pemanggilan_ibfk_3`;
ALTER TABLE `simapes`.`bk_pemanggilan` 
ADD INDEX `bk_pemanggilan_ibfk_3_idx` (`SISWA_PANGGIL` ASC),
DROP INDEX `SISWA_PANGGIL` ;
ALTER TABLE `simapes`.`bk_pemanggilan` 
ADD CONSTRAINT `bk_pemanggilan_ibfk_3`
  FOREIGN KEY (`SISWA_PANGGIL`)
  REFERENCES `simapes`.`md_siswa` (`ID_SISWA`)
  ON UPDATE CASCADE;
