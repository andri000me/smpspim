INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01104011000', 'TANGGAL CAWU', 'master_data/md_tanggal_cawu', 'index', '1', '2', '0');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01104011001', 'TANGGAL CAWU - ADD', 'master_data/md_tanggal_cawu', 'add', '0', '2', '0');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01104011002', 'TANGGAL CAWU - EDIT', 'master_data/md_tanggal_cawu', 'edit', '0', '2', '0');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01104011003', 'TANGGAL CAWU - DELETE', 'master_data/md_tanggal_cawu', 'delete', '0', '2', '0');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01104011000', '11');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01104011001', '11');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01104011002', '11');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01104011003', '11');
CREATE TABLE `md_tanggal_cawu` (
  `ID_TC` int(10) NOT NULL,
  `TA_TC` int(10) NOT NULL,
  `CAWU_TC` int(10) NOT NULL,
  `AWAL_TC` date NOT NULL,
  `AKHIR_TC` date NOT NULL,
  `USER_TC` int(10) NOT NULL,
  `CREATE_TC` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATE_TC` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `md_tanggal_cawu`
  ADD PRIMARY KEY (`ID_TC`),
  ADD UNIQUE KEY `TA_TC_2` (`TA_TC`,`CAWU_TC`),
  ADD KEY `TA_TC` (`TA_TC`),
  ADD KEY `CAWU_TC` (`CAWU_TC`);
ALTER TABLE `md_tanggal_cawu`
  MODIFY `ID_TC` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('1', '1', '2017-06-05', '2017-10-05', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('1', '2', '2017-10-05', '2017-01-05', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('1', '3', '2017-01-05', '2017-05-22', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('2', '1', '2017-07-17', '2017-10-17', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('2', '2', '2017-10-17', '2018-01-17', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('2', '3', '2018-01-17', '2018-06-25', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('3', '1', '2018-06-05', '2018-09-05', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('3', '2', '2018-09-05', '2018-12-05', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('3', '3', '2018-12-05', '2019-06-24', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('4', '1', '2019-06-24', '2019-10-24', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('4', '2', '2019-10-24', '2019-01-24', '1');
INSERT INTO `simapes`.`md_tanggal_cawu` (`TA_TC`, `CAWU_TC`, `AWAL_TC`, `AKHIR_TC`, `USER_TC`) VALUES ('4', '3', '2019-01-24', '2020-06-24', '1');
ALTER TABLE `md_tanggal_cawu` ADD FOREIGN KEY (`TA_TC`) REFERENCES `md_tahun_ajaran`(`ID_TA`) ON DELETE RESTRICT ON UPDATE CASCADE; ALTER TABLE `md_tanggal_cawu` ADD FOREIGN KEY (`CAWU_TC`) REFERENCES `md_catur_wulan`(`ID_CAWU`) ON DELETE RESTRICT ON UPDATE CASCADE; ALTER TABLE `md_tanggal_cawu` ADD FOREIGN KEY (`USER_TC`) REFERENCES `md_user`(`ID_USER`) ON DELETE RESTRICT ON UPDATE CASCADE;
UPDATE simapes.keu_setup
        JOIN
    keu_detail ON DETAIL_SETUP = ID_DT
        JOIN
    keu_tagihan ON TAGIHAN_DT = ID_TAG 
SET 
    KADALUARSA_SETUP = 1
WHERE
    TA_TAG = 2 AND STATUS_SETUP = 0
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01205000001', 'PEMBAYARAN', 'laporan/keuangan', 'index', '1', '2', '0');
UPDATE `simapes`.`md_menu` SET `CONTROLLER_MENU` = '#', `HAVE_CHILD` = '1' WHERE (`ID_MENU` = '01205000000');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01205000002', 'TAGIHAN', 'laporan/tagihan', 'index', '1', '2', '0');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('01205000003', 'TUNGGAKAN', 'laporan/tunggakan', 'index', '1', '2', '0');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01205000001', '12');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01205000002', '12');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('01205000003', '12');
UPDATE `simapes`.`md_menu` SET `CONTROLLER_MENU` = 'laporan/keuangan', `HAVE_CHILD` = '0' WHERE (`ID_MENU` = '01205000000');
DELETE FROM `simapes`.`md_menu` WHERE (`ID_MENU` = '01205000001');
DELETE FROM `simapes`.`md_menu` WHERE (`ID_MENU` = '01205000002');
DELETE FROM `simapes`.`md_menu` WHERE (`ID_MENU` = '01205000003');
UPDATE `simapes`.`md_menu` SET `CONTROLLER_MENU` = '#', `HAVE_CHILD` = '1' WHERE (`ID_MENU` = '00509000000');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('00509001000', 'GRAFIK HAFALAN', 'laporan/hafalan', 'index', '1', '2', '0');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('00509002000', 'LAPORAN CETAK', 'ph/laporan', 'index', '1', '2', '0');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('00509001000', '5');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('00509002000', '5');
