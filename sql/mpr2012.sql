-- phpMyAdmin SQL Dump
-- version 3.4.10.2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Sob 28. dub 2012, 14:51
-- Verze MySQL: 5.5.9
-- Verze PHP: 5.3.6

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databáze: `mpr2012`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `aktivita`
--

DROP TABLE IF EXISTS `aktivita`;
CREATE TABLE IF NOT EXISTS `aktivita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `vystup` int(11) NOT NULL,
  `zacatek` datetime NOT NULL,
  `konec` datetime NOT NULL,
  `zdroje` text COLLATE utf8_unicode_ci NOT NULL,
  `poradi` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aktivita_ibfk_1` (`vystup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;

--
-- Vypisuji data pro tabulku `aktivita`
--

INSERT INTO `aktivita` (`id`, `nazev`, `vystup`, `zacatek`, `konec`, `zdroje`, `poradi`) VALUES
(19, 'asdf', 13, '2012-04-30 00:00:00', '2012-04-30 00:00:00', 'asdf', 0),
(26, 'asdf', 13, '1970-01-01 00:00:00', '1970-01-01 00:00:00', '', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `clen`
--

DROP TABLE IF EXISTS `clen`;
CREATE TABLE IF NOT EXISTS `clen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uzivatel` int(11) NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`),
  KEY `uzivatel` (`uzivatel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `matice`
--

DROP TABLE IF EXISTS `matice`;
CREATE TABLE IF NOT EXISTS `matice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `zamer` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cil` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

--
-- Vypisuji data pro tabulku `matice`
--

INSERT INTO `matice` (`id`, `nazev`, `zamer`, `cil`) VALUES
(1, 'První testovací matice', 'Pomoci svetu', 'Uklidit ulice'),
(3, 'Prázdná matice', 'Empty prostě', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `novinka`
--

DROP TABLE IF EXISTS `novinka`;
CREATE TABLE IF NOT EXISTS `novinka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `novinka`
--

INSERT INTO `novinka` (`id`, `header`, `text`, `date`, `author`) VALUES
(1, 'První novinka', 'Právě byla spuštěna první verze webového portálu týmu Ones - MPR 2012.', '2012-03-25 10:32:10', 2),
(3, 'Novinky - výpis', 'Implementován výpis novinek na úvodní domovskou stránku webu.', '2012-03-25 10:52:15', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `predpoklad`
--

DROP TABLE IF EXISTS `predpoklad`;
CREATE TABLE IF NOT EXISTS `predpoklad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `radek` enum('2','3','4','5') COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `predpoklad`
--

INSERT INTO `predpoklad` (`id`, `nazev`, `radek`, `matice`) VALUES
(1, 'aUSA', '2', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `ukazatel`
--

DROP TABLE IF EXISTS `ukazatel`;
CREATE TABLE IF NOT EXISTS `ukazatel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `radek` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `ukazatel`
--

INSERT INTO `ukazatel` (`id`, `nazev`, `radek`, `matice`) VALUES
(1, 'Male', '1', 1),
(2, 'Neco dale', '1', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `uzivatel`
--

DROP TABLE IF EXISTS `uzivatel`;
CREATE TABLE IF NOT EXISTS `uzivatel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `jmeno` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `prijmeni` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `skupina` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Výchozí',
  `role` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `uzivatel`
--

INSERT INTO `uzivatel` (`id`, `username`, `password`, `jmeno`, `prijmeni`, `skupina`, `role`) VALUES
(2, 'xzajic07@stud.fit.vutbr.cz', '912ec803b2ce49e4a541068d495ab570', 'Jiří', 'Zajíc', 'Výchozí', ''),
(3, 'xbedna33@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(4, 'xpodiv01@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(5, 'xranda00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(6, 'xpodho02@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(7, 'xmarci00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(8, 'xkrize06@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(9, 'xcekan00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(10, 'xkucer60@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', ''),
(11, 'xtothr00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '', '', 'Výchozí', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `vystup`
--

DROP TABLE IF EXISTS `vystup`;
CREATE TABLE IF NOT EXISTS `vystup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  `poradi` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Vypisuji data pro tabulku `vystup`
--

INSERT INTO `vystup` (`id`, `nazev`, `matice`, `poradi`) VALUES
(13, '111', 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `zdroj_overeni`
--

DROP TABLE IF EXISTS `zdroj_overeni`;
CREATE TABLE IF NOT EXISTS `zdroj_overeni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `radek` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL,
  `matice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matice` (`matice`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `zdroj_overeni`
--

INSERT INTO `zdroj_overeni` (`id`, `nazev`, `radek`, `matice`) VALUES
(3, 'Wikipedia', '1', 1),
(4, 'Google', '1', 1),
(5, 'Lorem ipsum', '3', 1);

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `aktivita`
--
ALTER TABLE `aktivita`
  ADD CONSTRAINT `aktivita_ibfk_1` FOREIGN KEY (`vystup`) REFERENCES `vystup` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `clen`
--
ALTER TABLE `clen`
  ADD CONSTRAINT `clen_ibfk_2` FOREIGN KEY (`uzivatel`) REFERENCES `uzivatel` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clen_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `novinka`
--
ALTER TABLE `novinka`
  ADD CONSTRAINT `novinka_ibfk_1` FOREIGN KEY (`author`) REFERENCES `uzivatel` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `predpoklad`
--
ALTER TABLE `predpoklad`
  ADD CONSTRAINT `predpoklad_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `ukazatel`
--
ALTER TABLE `ukazatel`
  ADD CONSTRAINT `ukazatel_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `vystup`
--
ALTER TABLE `vystup`
  ADD CONSTRAINT `vystup_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `zdroj_overeni`
--
ALTER TABLE `zdroj_overeni`
  ADD CONSTRAINT `zdroj_overeni_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
