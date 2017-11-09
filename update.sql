UPDATE `akad_kelas` SET `NAMA_KELAS` = '1 D. Ula E Banin' WHERE `akad_kelas`.`ID_KELAS` = 120;

CREATE TABLE `md_status_kk` (
  `ID_SKK` int(12) NOT NULL,
  `NAMA_SKK` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `md_status_kk` (`ID_SKK`, `NAMA_SKK`) VALUES
(1, 'Ayah Kandung'),
(2, 'Ibu Kandung'),
(3, 'Kakek'),
(4, 'Nenek'),
(5, 'Kakak'),
(6, 'Paman'),
(7, 'Bibi'),
(8, 'Lainnya');
ALTER TABLE `md_status_kk`
  ADD PRIMARY KEY (`ID_SKK`);
ALTER TABLE `md_status_kk`
  MODIFY `ID_SKK` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `md_siswa` ADD `STATUS_KK_SANTRI` INT(12) NOT NULL AFTER `KK_SISWA`, ADD INDEX (`STATUS_KK_SANTRI`);
UPDATE `md_siswa` SET STATUS_KK_SANTRI=1;
ALTER TABLE `md_siswa` ADD FOREIGN KEY (`STATUS_KK_SANTRI`) REFERENCES `simapes_9`.`md_status_kk`(`ID_SKK`) ON DELETE RESTRICT ON UPDATE CASCADE;

INSERT INTO `md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `ICON_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01103011000', 'STATUS KK', 'master_data/status_kk', 'index', NULL, '1', '2', '0'), ('01103011001', 'STATUS KK - ADD', 'master_data/status_kk', 'add', NULL, '0', '2', '0'), ('01103011002', 'STATUS KK - EDIT', 'master_data/status_kk', 'edit', NULL, '0', '2', '0'), ('01103011003', 'STATUS KK - DELETE', 'master_data/status_kk', 'delete', NULL, '0', '2', '0');
INSERT INTO `md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01103011000', '11'), ('01103011001', '11'), ('01103011002', '11'), ('01103011003', '11')

ALTER TABLE `md_siswa` CHANGE `STATUS_KK_SANTRI` `STATUS_KK_SISWA` INT(12) NOT NULL;
ALTER TABLE `md_siswa` CHANGE `STATUS_KK_SISWA` `STATUS_KK_SISWA` INT(12) NULL DEFAULT NULL;
INSERT INTO `md_hobi` (`ID_HOBI`, `NAMA_HOBI`) VALUES (NULL, 'Olahraga'), (NULL, 'Kesenian'), (NULL, 'Membaca'), (NULL, 'Menulis'), (NULL, 'Travelling'), (NULL, 'Lainnya');
INSERT INTO `md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `ICON_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01103012000', 'HOBI', 'master_data/hobi', 'index', NULL, '1', '2', '0'), ('01103012001', 'HOBI - ADD', 'master_data/hobi', 'add', NULL, '0', '2', '0'), ('01103012002', 'HOBI - EDIT', 'master_data/hobi', 'edit', NULL, '0', '2', '0'), ('01103012003', 'HOBI - DELETE', 'master_data/hobi', 'delete', NULL, '0', '2', '0');
INSERT INTO `md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01103012000', '11'), ('01103012001', '11'), ('01103012002', '11'), ('01103012003', '11');


CREATE TABLE `md_cita` (
  `ID_CITA` int(12) NOT NULL,
  `NAMA_CITA` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `md_cita` (`ID_CITA`, `NAMA_CITA`) VALUES
(1, 'PNS'),
(2, 'TNI/POLRI'),
(3, 'Guru/Dosen'),
(4, 'Dokter'),
(5, 'Politikus'),
(6, 'Wiraswasta'),
(7, 'Pekerja Senu/Lukis/Artis/Sejenis'),
(8, 'Lainya');
ALTER TABLE `md_cita`
  ADD PRIMARY KEY (`ID_CITA`);
ALTER TABLE `md_cita`
  MODIFY `ID_CITA` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `md_siswa` ADD `CITA_SISWA` INT(12) NOT NULL AFTER `HOBI_SISWA`, ADD INDEX (`CITA_SISWA`);
ALTER TABLE `md_siswa` CHANGE `CITA_SISWA` `CITA_SISWA` INT(12) NULL DEFAULT NULL;
UPDATE md_siswa SET CITA_SISWA=NULL;
ALTER TABLE `md_siswa` ADD FOREIGN KEY (`CITA_SISWA`) REFERENCES `simapes_9`.`md_cita`(`ID_CITA`) ON DELETE RESTRICT ON UPDATE CASCADE;

INSERT INTO `md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `ICON_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01103013000', 'CITA', 'master_data/cita', 'index', NULL, '1', '2', '0'), ('01103013001', 'CITA - ADD', 'master_data/cita', 'add', NULL, '0', '2', '0'), ('01103013002', 'CITA - EDIT', 'master_data/cita', 'edit', NULL, '0', '2', '0'), ('01103013003', 'CITA - DELETE', 'master_data/cita', 'delete', NULL, '0', '2', '0');
INSERT INTO `md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01103013000', '11'), ('01103013001', '11'), ('01103013002', '11'), ('01103013003', '11');