INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('00509001000', 'PERPONDOK', 'ph/laporan_perpondok', 'index', '1', '2', '0');
UPDATE `simapes`.`md_menu` SET `CONTROLLER_MENU` = '#', `FUNCTION_MENU` = null, `HAVE_CHILD` = '1' WHERE (`ID_MENU` = '00509000000');
INSERT INTO `simapes`.`md_menu` (`ID_MENU`, `NAME_MENU`, `CONTROLLER_MENU`, `FUNCTION_MENU`, `SHOW_MENU`, `LEVEL_CHILD`, `HAVE_CHILD`) VALUES ('00509002000', 'GRAFIK', 'laporan/hafalan', 'index', '1', '2', '0');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('00509002000', '5');
INSERT INTO `simapes`.`md_levelmenu` (`MENU_LEVELMENU`, `HAKAKSES_LEVELMENU`) VALUES ('00509001000', '5');
