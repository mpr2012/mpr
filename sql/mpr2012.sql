
<!-- saved from url=(0069)http://localhost:8080/mpr/adminer/?username=root&db=mpr&dump=uzivatel -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css" style="display: none !important;">object:not([type]),object[classid$=":D27CDB6E-AE6D-11cf-96B8-444553540000"],object[classid$=":d27cdb6e-ae6d-11cf-96b8-444553540000"],object[codebase*="swflash.cab"],object[data*=".swf"],embed[type="application/x-shockwave-flash"],embed[src*=".swf"],object[type="application/x-shockwave-flash"],object[src*=".swf"],object[codetype="application/x-shockwave-flash"],iframe[type="application/x-shockwave-flash"],object[classid$=":166B1BCA-3F9C-11CF-8075-444553540000"],object[codebase*="sw.cab"],object[data*=".dcr"],embed[type="application/x-director"],embed[src*=".dcr"],object[type="application/x-director"],object[src*=".dcr"],object[classid$=":15B782AF-55D8-11D1-B477-006097098764"],object[codebase*="awswaxf.cab"],object[data*=".aam"],embed[type="application/x-authorware-map"],embed[src*=".aam"],object[type="application/x-authorware-map"],object[src*=".aam"],object[classid*="32C73088-76AE-40F7-AC40-81F62CB2C1DA"],object[type="application/ag-plugin"],object[type="application/x-silverlight"],object[type="application/x-silverlight-2"],object[source*=".xaml"],object[sourceelement*="xaml"],embed[type="application/ag-plugin"],embed[source*=".xaml"]{display: none !important;}</style><style type="text/css"></style></head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">-- Adminer 3.3.1 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `aktivita`;
CREATE TABLE `aktivita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` text COLLATE utf8_unicode_ci NOT NULL,
  `vystup` int(11) NOT NULL,
  `zacatek` datetime NOT NULL,
  `konec` datetime NOT NULL,
  `zdroje` text COLLATE utf8_unicode_ci NOT NULL,
  `poradi` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aktivita_ibfk_1` (`vystup`),
  CONSTRAINT `aktivita_ibfk_1` FOREIGN KEY (`vystup`) REFERENCES `vystup` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `aktivita` (`id`, `nazev`, `vystup`, `zacatek`, `konec`, `zdroje`, `poradi`) VALUES
(19,	'asdf',	13,	'2012-04-13 00:00:00',	'2012-04-30 00:00:00',	'asdf',	0),
(26,	'asdf',	13,	'1970-01-01 00:00:00',	'1970-01-01 00:00:00',	'',	0),
(27,	'setkani se satanem',	16,	'2012-04-01 00:00:00',	'2012-04-28 00:00:00',	'zaruvzdorny odev',	0),
(31,	'Specifikace požadavků',	18,	'2012-03-08 00:00:00',	'2012-03-20 00:00:00',	'David Bednář, Martin Křížek, Jakub Randa',	0);

DROP TABLE IF EXISTS `clen`;
CREATE TABLE `clen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uzivatel` int(11) NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`),
  KEY `uzivatel` (`uzivatel`),
  CONSTRAINT `clen_ibfk_2` FOREIGN KEY (`uzivatel`) REFERENCES `uzivatel` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clen_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `clen` (`id`, `uzivatel`, `matice`) VALUES
(34,	2,	1),
(35,	2,	3),
(37,	2,	29),
(38,	2,	30),
(39,	10,	31),
(40,	2,	31),
(42,	9,	33),
(43,	3,	34),
(44,	2,	34),
(45,	4,	34),
(46,	5,	34),
(47,	6,	34),
(48,	7,	34),
(49,	8,	34),
(50,	9,	34),
(51,	10,	34),
(52,	11,	34);

DROP TABLE IF EXISTS `matice`;
CREATE TABLE `matice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `zamer` text COLLATE utf8_unicode_ci NOT NULL,
  `cil` text COLLATE utf8_unicode_ci NOT NULL,
  `majitel` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `majitel` (`majitel`),
  CONSTRAINT `matice_ibfk_1` FOREIGN KEY (`majitel`) REFERENCES `uzivatel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `matice` (`id`, `nazev`, `zamer`, `cil`, `majitel`) VALUES
(1,	'První testovací matice',	'Pomoci svetu',	'Uklidit ulice',	2),
(3,	'Prázdná matice',	'Empty prostě',	'',	2),
(26,	'asdf',	'',	'',	3),
(29,	'Owner',	'',	'',	2),
(30,	'Member (only)',	'',	'',	3),
(31,	'Moje matice',	'',	'',	10),
(33,	'Test',	'',	'',	9),
(34,	'Ukázková matice',	'Úspěšné absolvování předmětu M',	'Vytvořit Aplikaci pro podporu ',	3);

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `news` (`id`, `header`, `text`, `date`, `author`) VALUES
(1,	'První novinka',	'Právě byla spuštěna první verze webového portálu týmu Ones - MPR 2012.',	'2012-03-25 12:32:10',	2),
(3,	'Novinky - výpis',	'Implementován výpis novinek na úvodní domovskou stránku webu.',	'2012-03-25 12:52:15',	2);

DROP TABLE IF EXISTS `novinka`;
CREATE TABLE `novinka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  CONSTRAINT `novinka_ibfk_1` FOREIGN KEY (`author`) REFERENCES `uzivatel` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `novinka` (`id`, `header`, `text`, `date`, `author`) VALUES
(1,	'První novinka',	'Právě byla spuštěna první verze webového portálu týmu Ones - MPR 2012.',	'2012-03-25 12:32:10',	2),
(3,	'Novinky - výpis',	'Implementován výpis novinek na úvodní domovskou stránku webu.',	'2012-03-25 12:52:15',	2);

DROP TABLE IF EXISTS `predpoklad`;
CREATE TABLE `predpoklad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` text COLLATE utf8_unicode_ci NOT NULL,
  `radek` enum('2','3','4','5') COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`),
  CONSTRAINT `predpoklad_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `predpoklad` (`id`, `nazev`, `radek`, `matice`) VALUES
(1,	'aUSA',	'2',	1),
(2,	'Úspěšně obhájíme projekt',	'2',	34),
(3,	'Úspěšně složíme zkoušku',	'2',	34),
(4,	'Je vytvořený tým',	'5',	34),
(5,	'Všechny dokumenty budou komple',	'3',	34),
(6,	'Aplikace bude fungovat',	'3',	34),
(7,	'Daná etapa bude uzavřena',	'4',	34);

DROP TABLE IF EXISTS `ukazatel`;
CREATE TABLE `ukazatel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` text COLLATE utf8_unicode_ci NOT NULL,
  `radek` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`),
  CONSTRAINT `ukazatel_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `ukazatel` (`id`, `nazev`, `radek`, `matice`) VALUES
(1,	'Male',	'1',	1),
(2,	'Neco dale',	'1',	1),
(5,	'Počet bodů za vytvořenou aplik',	'2',	34),
(6,	'Počet bodů každého člena týmu ',	'1',	34),
(7,	'hodne, ale hodne dlouhy text, tak dlouhy, ze by se sem nemusel vejit, co kdyz tady bude fakt toho hodne, to vam rikam, panove',	'3',	34),
(8,	'dalsi, opravdu docela dlouhy popisek, uvidime, jak to bude vypadat, az tu budou dva takovi podobni',	'3',	34);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2,	'xzajic07@stud.fit.vutbr.cz',	'912ec803b2ce49e4a541068d495ab570',	''),
(3,	'xbedna33@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(4,	'xpodiv01@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(5,	'xranda00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(6,	'xpodho02@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(7,	'xmarci00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(8,	'xkrize06@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(9,	'xcekan00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(10,	'xkucer60@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	''),
(11,	'xtothr00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'');

DROP TABLE IF EXISTS `uzivatel`;
CREATE TABLE `uzivatel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `jmeno` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `prijmeni` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `skupina` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Výchozí',
  `role` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `uzivatel` (`id`, `username`, `password`, `jmeno`, `prijmeni`, `skupina`, `role`) VALUES
(2,	'xzajic07@stud.fit.vutbr.cz',	'7f533d7de2bd0e454f920f608d42986d',	'Jiří',	'Zajíc',	'Implementace',	''),
(3,	'xbedna33@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'David',	'Bednář',	'Analýza',	''),
(4,	'xpodiv01@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Jakub',	'Podivínský',	'Plánování',	''),
(5,	'xranda00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Jakub',	'Randa',	'Analýza',	''),
(6,	'xpodho02@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Jiří',	'Podhorský',	'Návrh',	''),
(7,	'xmarci00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Marcel',	'Mačiš',	'Implementace',	''),
(8,	'xkrize06@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Martin',	'Křížek',	'Návrh',	''),
(9,	'xcekan00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Ondřej',	'Čekan',	'Plánování',	''),
(10,	'xkucer60@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Petr',	'Kučera',	'Implementace',	''),
(11,	'xtothr00@stud.fit.vutbr.cz',	'50a1e8d0ea071aca23f99488fd969483',	'Róbert',	'Toth',	'Návrh',	'');

DROP TABLE IF EXISTS `vystup`;
CREATE TABLE `vystup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` text COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  `poradi` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`),
  CONSTRAINT `vystup_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `vystup` (`id`, `nazev`, `matice`, `poradi`) VALUES
(13,	'111',	1,	1),
(16,	'satanska bible',	31,	10),
(18,	'Analýza velmi velmi veli velmi velmi dlouha',	34,	1),
(19,	'Návrh',	34,	3),
(20,	'Rizika',	34,	2);

DROP TABLE IF EXISTS `zdroj_overeni`;
CREATE TABLE `zdroj_overeni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` text COLLATE utf8_unicode_ci NOT NULL,
  `radek` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`),
  CONSTRAINT `zdroj_overeni_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `zdroj_overeni` (`id`, `nazev`, `radek`, `matice`) VALUES
(3,	'Wikipedia',	'1',	1),
(4,	'Google',	'1',	1),
(5,	'Lorem ipsum',	'3',	1),
(6,	'Hodnocení v informačním systém',	'2',	34),
(7,	'Informační systém FIT',	'1',	34);

-- 2012-05-01 22:38:57
</pre></body></html>