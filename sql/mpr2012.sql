-- phpMyAdmin SQL Dump
-- version 3.4.10.2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Ned 22. dub 2012, 20:14
-- Verze MySQL: 5.5.9
-- Verze PHP: 5.3.6

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `aktivita`
--

INSERT INTO `aktivita` (`id`, `nazev`, `vystup`, `zacatek`, `konec`, `zdroje`, `poradi`) VALUES
(1, 'Ukol 1', 1, '2012-04-10 12:11:06', '2012-04-26 07:29:49', 'Zdroj 1', 10),
(2, 'Ukol 2', 1, '2012-04-11 00:00:00', '2012-04-19 00:00:00', 'Zdroj asdf', 20),
(3, 'asdf', 1, '2012-00-00 00:00:00', '2012-00-00 00:00:00', '', 0),
(4, 'Něco', 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0);

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
  KEY `matice` (`matice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Vypisuji data pro tabulku `matice`
--

INSERT INTO `matice` (`id`, `nazev`, `zamer`, `cil`) VALUES
(1, 'První testovací matice', 'Pomoci svetu', 'Uklidit ulice'),
(2, 'Druhá testovací matice', 'Nevim', 'Vůbec nevim'),
(3, 'Prázdná matice', 'Empty prostě', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `news`
--

INSERT INTO `news` (`id`, `header`, `text`, `date`, `author`) VALUES
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
(1, 'USA', '2', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `ukazatel`
--

INSERT INTO `ukazatel` (`id`, `nazev`, `radek`, `matice`) VALUES
(1, 'Male', '1', 1),
(2, 'Neco dale', '1', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2, 'xzajic07@stud.fit.vutbr.cz', '912ec803b2ce49e4a541068d495ab570', ''),
(3, 'xbedna33@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(4, 'xpodiv01@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(5, 'xranda00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(6, 'xpodho02@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(7, 'xmarci00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(8, 'xkrize06@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(9, 'xcekan00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(10, 'xkucer60@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', ''),
(11, 'xtothr00@stud.fit.vutbr.cz', '50a1e8d0ea071aca23f99488fd969483', '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vystup`
--

INSERT INTO `vystup` (`id`, `nazev`, `matice`, `poradi`) VALUES
(1, 'Vystup a', 1, 10),
(2, 'Vystup b', 1, 20),
(4, 'Výstup druhé matice', 2, 10),
(5, 'Výstup 5', 1, 0);

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
  ADD CONSTRAINT `aktivita_ibfk_1` FOREIGN KEY (`vystup`) REFERENCES `vystup` (`id`);

--
-- Omezení pro tabulku `clen`
--
ALTER TABLE `clen`
  ADD CONSTRAINT `clen_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`);

--
-- Omezení pro tabulku `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `predpoklad`
--
ALTER TABLE `predpoklad`
  ADD CONSTRAINT `predpoklad_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`);

--
-- Omezení pro tabulku `ukazatel`
--
ALTER TABLE `ukazatel`
  ADD CONSTRAINT `ukazatel_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`);

--
-- Omezení pro tabulku `vystup`
--
ALTER TABLE `vystup`
  ADD CONSTRAINT `vystup_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`);

--
-- Omezení pro tabulku `zdroj_overeni`
--
ALTER TABLE `zdroj_overeni`
  ADD CONSTRAINT `zdroj_overeni_ibfk_1` FOREIGN KEY (`matice`) REFERENCES `matice` (`id`);
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
