UPDATE `md_menu` SET `ID_MENU` = '00406004000' WHERE `md_menu`.`ID_MENU` = '00406003000';
UPDATE `md_menu` SET `ID_MENU` = '00406003000' WHERE `md_menu`.`ID_MENU` = '00406002000';
UPDATE `md_menu` SET `ID_MENU` = '00406002000' WHERE `md_menu`.`ID_MENU` = '00406001000';
INSERT INTO `md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `ICON_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('00406001000', 'SALDO', 'keuangan/laporan_saldo', 'index', NULL, '1', '2', '0');
INSERT INTO `md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('00406001000', '4');


INSERT INTO `md_pengaturan` (`ID_PENGATURAN`, `NAMA_PENGATURAN`, `EDITABLE_PENGATURAN`, `USER_PENGATURAN`, `CREATED_PENGATURAN`, `ORDER_PENGATURAN`) VALUES ('pd_tu_dan_keuangan_1', '2', '0', '1', CURRENT_TIMESTAMP, NULL), ('pd_tu_dan_keuangan_2', '4', '0', '1', CURRENT_TIMESTAMP, NULL);
UPDATE `md_pengaturan` SET `EDITABLE_PENGATURAN` = '1' WHERE `md_pengaturan`.`ID_PENGATURAN` = 'pd_tu_dan_keuangan_1';
UPDATE `md_pengaturan` SET `EDITABLE_PENGATURAN` = '1' WHERE `md_pengaturan`.`ID_PENGATURAN` = 'pd_tu_dan_keuangan_2';
UPDATE `md_pengaturan` SET `ORDER_PENGATURAN` = '25' WHERE `md_pengaturan`.`ID_PENGATURAN` = 'pd_tu_dan_keuangan_1';
UPDATE `md_pengaturan` SET `ORDER_PENGATURAN` = '26' WHERE `md_pengaturan`.`ID_PENGATURAN` = 'pd_tu_dan_keuangan_2';
