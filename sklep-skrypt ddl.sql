-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 31 Sty 2020, 21:57
-- Wersja serwera: 10.1.38-MariaDB
-- Wersja PHP: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `sklep`
--

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `zmien_ilosc` (IN `produkt` INT, IN `licz` INT, IN `mag` VARCHAR(100), IN `tryb` INT)  NO SQL
IF tryb=0 THEN 
UPDATE stan_magazynowy set zasob=zasob+licz, data_stanu=CURRENT_DATE WHERE produkt_nr_produktu = produkt AND magazyn_nazwa=mag;
ELSEIF tryb=1 THEN 
UPDATE stan_magazynowy set zasob=zasob-licz, data_stanu=CURRENT_DATE WHERE produkt_nr_produktu = produkt AND magazyn_nazwa=mag;
END IF$$

--
-- Funkcje
--
CREATE DEFINER=`root`@`localhost` FUNCTION `wartosc` (`zamowienie` INT, `tryb` INT) RETURNS FLOAT NO SQL
BEGIN
DECLARE cena FLOAT;
IF tryb=0 THEN
select sum(z.ilosc*p.cena_netto) into cena from produkt p, zamowienie_produkt z where z.produkt_nr_produktu=p.nr_produktu and z.zamowienia_nr_zamowienia=zamowienie;
ELSEIF tryb=1 THEN
    select sum(z.ilosc*p.cena_brutto) into cena from produkt p, zamowienie_produkt z where z.produkt_nr_produktu=p.nr_produktu and z.zamowienia_nr_zamowienia=zamowienie;
END IF;
RETURN cena;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `adres`
--

CREATE TABLE `adres` (
  `id_adresu` int(11) NOT NULL,
  `miasto` varchar(4000) NOT NULL,
  `kod_pocztowy` varchar(4000) NOT NULL,
  `ulica` varchar(4000) NOT NULL,
  `nr_domu` varchar(11) NOT NULL,
  `nr_mieszkania` int(11) DEFAULT NULL,
  `klient_id_klienta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `adres`
--

INSERT INTO `adres` (`id_adresu`, `miasto`, `kod_pocztowy`, `ulica`, `nr_domu`, `nr_mieszkania`, `klient_id_klienta`) VALUES
(2, 'OstrÃ³w', '63-401', 'Olszowa', '45', 12, 1),
(3, 'PoznaÅ„', '61-008', 'Smolna', '13b', 192, 2),
(4, 'OstrÃ³w Wielkopolski', '63-400', 'Limanowskiego', '22', NULL, 3),
(5, 'OstrÃ³w', '61-008', 'Olszowa', '22', NULL, 4),
(7, 'PoznaÅ„', '61-008', 'BuÅ‚garska', '17', NULL, 5);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `faktura_sprzedazy`
--

CREATE TABLE `faktura_sprzedazy` (
  `nr_faktury` int(11) NOT NULL,
  `data_sprzedazy` date NOT NULL,
  `wartosc_netto` float NOT NULL,
  `wartosc_brutto` float NOT NULL,
  `wartosc_vat` float NOT NULL,
  `zamowienia_nr_zamowienia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `faktura_sprzedazy`
--

INSERT INTO `faktura_sprzedazy` (`nr_faktury`, `data_sprzedazy`, `wartosc_netto`, `wartosc_brutto`, `wartosc_vat`, `zamowienia_nr_zamowienia`) VALUES
(1, '2020-01-25', 1178, 1612, 434, 1),
(2, '2020-01-25', 11, 14, 3, 3),
(3, '2020-01-25', 50, 61, 11, 4),
(4, '2020-01-25', 70, 85, 15, 6),
(5, '2020-01-25', 200, 246, 46, 7),
(6, '2020-01-25', 693, 900, 207, 11),
(7, '2020-01-26', 222, 323, 101, 18),
(8, '2020-01-26', 1116, 1485, 369, 19),
(9, '2020-01-27', 330, 405, 75, 20),
(10, '2020-01-27', 934, 1212, 278, 21),
(11, '2020-01-27', 60, 72, 12, 22),
(12, '2020-01-27', 10, 12, 2, 23),
(13, '2020-01-27', 10, 12, 2, 24),
(14, '2020-01-27', 21, 26, 5, 25),
(15, '2020-01-27', 30, 36, 6, 26),
(16, '2020-01-27', 84, 104, 20, 27),
(17, '2020-01-27', 409, 588, 179, 28),
(18, '2020-01-27', 10, 10.5, 0.5, 29),
(19, '2020-01-27', 10, 10.5, 0.5, 30),
(20, '2020-01-27', 10, 10.5, 0.5, 31),
(21, '2020-01-30', 10, 12.3, 2.3, 32),
(22, '2020-01-30', 363, 446.49, 83.49, 33),
(23, '2020-01-30', 75, 78.75, 3.75, 34),
(24, '2020-01-30', 45, 47.25, 2.25, 35),
(25, '2020-01-30', 10, 12.3, 2.3, 36),
(26, '2020-01-30', 11, 13.53, 2.53, 37),
(27, '2020-01-30', 11, 13.53, 2.53, 38),
(28, '2020-01-30', 123, 151.29, 28.29, 39),
(29, '2020-01-30', 100, 123, 23, 40),
(30, '2020-01-30', 70, 86.1, 16.1, 41),
(31, '2020-01-30', 58.5, 70.16, 11.66, 42),
(32, '2020-01-31', 163, 200.49, 37.49, 43);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategoria`
--

CREATE TABLE `kategoria` (
  `kategoria_id` int(11) NOT NULL,
  `nazwa_kategorii` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `kategoria`
--

INSERT INTO `kategoria` (`kategoria_id`, `nazwa_kategorii`) VALUES
(5, 'KoszykÃ³wka'),
(1, 'PiÅ‚ka noÅ¼na'),
(4, 'PiÅ‚ka rÄ™czna'),
(6, 'SiatkÃ³wka'),
(7, 'Szachy');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE `klient` (
  `id_klienta` int(11) NOT NULL,
  `imie` varchar(4000) NOT NULL,
  `nazwisko` varchar(4000) NOT NULL,
  `login` varchar(4000) NOT NULL,
  `haslo` varchar(4000) NOT NULL,
  `rodzaj_klienta` char(1) NOT NULL,
  `nazwa_firmy` varchar(4000) DEFAULT NULL,
  `regon` int(11) DEFAULT NULL,
  `nip` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `klient`
--

INSERT INTO `klient` (`id_klienta`, `imie`, `nazwisko`, `login`, `haslo`, `rodzaj_klienta`, `nazwa_firmy`, `regon`, `nip`) VALUES
(1, 'Jakub', 'GÃ³rny', 'jgorny', 'zaq1', 'k', '', 0, 0),
(2, 'Mateusz', 'Tamborski', 'mtamborski', 'zaq1', 'k', '', 0, 0),
(3, 'Roman', 'GÃ³rny', 'rgorny', 'qwerty', 'f', 'Pi-Ro', 123456785, 777777777),
(4, 'Jan', 'Kowalski', 'jkowalski', 'zaq1', 'k', '', 0, 0),
(5, 'Piotr', 'Nowak', 'pnowak', 'zaq1', 'k', '', 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kontakt_klient`
--

CREATE TABLE `kontakt_klient` (
  `id_kontaktu` int(11) NOT NULL,
  `nr_telefonu` int(11) NOT NULL,
  `email` varchar(4000) NOT NULL,
  `klient_id_klienta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `kontakt_klient`
--

INSERT INTO `kontakt_klient` (`id_kontaktu`, `nr_telefonu`, `email`, `klient_id_klienta`) VALUES
(1, 505451003, 'kksek@o2.pl', 1),
(2, 111222333, 'test@piro.com', 3),
(3, 333333, 'test@gmail.com', 4),
(4, 444444, 'ssss@gmail.com', 4),
(6, 123456789, 'example@gmail.com', 5);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kontakt_pracownik`
--

CREATE TABLE `kontakt_pracownik` (
  `id_kontaktu` int(11) NOT NULL,
  `nr_telefonu` int(11) NOT NULL,
  `email` varchar(4000) NOT NULL,
  `pracownik_id_pracownika` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `kontakt_pracownik`
--

INSERT INTO `kontakt_pracownik` (`id_kontaktu`, `nr_telefonu`, `email`, `pracownik_id_pracownika`) VALUES
(1, 505451002, 'kksek@tlen.pl', 1),
(2, 987654321, 'test2@mail.com', 2),
(4, 123456789, 'test@gmail.com', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `magazyn`
--

CREATE TABLE `magazyn` (
  `nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `magazyn`
--

INSERT INTO `magazyn` (`nazwa`) VALUES
('magazyn1'),
('magazyn2'),
('magazyn3');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
--

CREATE TABLE `pracownik` (
  `id_pracownika` int(11) NOT NULL,
  `imie` varchar(4000) NOT NULL,
  `nazwisko` varchar(4000) NOT NULL,
  `placa` float NOT NULL,
  `data_zatrudnienia` date NOT NULL,
  `login` varchar(4000) NOT NULL,
  `haslo` varchar(4000) NOT NULL,
  `magazyn_nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `pracownik`
--

INSERT INTO `pracownik` (`id_pracownika`, `imie`, `nazwisko`, `placa`, `data_zatrudnienia`, `login`, `haslo`, `magazyn_nazwa`) VALUES
(1, 'Jakub', 'GÃ³rny', 1488, '2020-01-25', 'admin', 'zaq1', 'magazyn1'),
(2, 'Adam', 'MaÅ‚ysz', 102, '2020-01-26', 'amalysz', 'qwerty', 'magazyn2'),
(3, 'Joanna', 'Borowiak', 1922, '2020-01-26', 'jborowiak', 'qwerty', 'magazyn1'),
(4, 'Mateusz', 'Tamborski', 1800, '2020-01-26', 'mtamborski', 'qwerty', 'magazyn2');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `producent`
--

CREATE TABLE `producent` (
  `producent_id` int(11) NOT NULL,
  `nazwa_producenta` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `producent`
--

INSERT INTO `producent` (`producent_id`, `nazwa_producenta`) VALUES
(1, 'Adidas'),
(9, 'Inne Nike'),
(6, 'Nike'),
(7, 'Reebok');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkt`
--

CREATE TABLE `produkt` (
  `nr_produktu` int(11) NOT NULL,
  `nazwa` varchar(4000) NOT NULL,
  `cena_netto` float NOT NULL,
  `cena_brutto` float NOT NULL,
  `procent_vat` float NOT NULL,
  `rozmiar` varchar(4000) NOT NULL,
  `opis` varchar(4000) DEFAULT NULL,
  `kategoria_kategoria_id` int(11) NOT NULL,
  `producent_producent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `produkt`
--

INSERT INTO `produkt` (`nr_produktu`, `nazwa`, `cena_netto`, `cena_brutto`, `procent_vat`, `rozmiar`, `opis`, `kategoria_kategoria_id`, `producent_producent_id`) VALUES
(3, 'Koszulka', 231, 284.13, 23, 'S', 'Koszulka piÅ‚karska', 1, 7),
(6, 'Koszulka', 123, 151.29, 23, 'S', 'Koszulka piÅ‚karska', 1, 1),
(7, 'Koszulka', 231, 284.13, 23, 'M', 'Koszulka piÅ‚karska', 1, 7),
(8, 'Koszulka', 231, 284.13, 23, 'L', 'Koszulka piÅ‚karska', 1, 7),
(9, 'Spodnie', 100, 123, 23, 'S', 'Spodnie piÅ‚karskie', 1, 6),
(10, 'PiÅ‚ka', 50, 61.5, 23, '5', 'PiÅ‚ka do gry w piÅ‚ke noÅ¼nÄ…', 1, 6),
(11, 'Getry', 10, 12.3, 23, 'S', 'Getry piÅ‚karskie', 1, 6),
(12, 'Spodnie', 100, 123, 23, 'S', 'Spodnie piÅ‚karskie', 1, 1),
(13, 'PiÅ‚ka', 70, 86.1, 23, '5', 'PiÅ‚ka do gry w piÅ‚ke noÅ¼nÄ…', 1, 1),
(14, 'Getry', 11, 13.53, 23, 'S', 'Getry piÅ‚karskie', 1, 7),
(15, 'Koszulka', 123, 151.29, 23, 'M', 'Koszulka piÅ‚karska', 1, 1),
(16, 'Koszulka', 123, 151.29, 23, 'L', 'Koszulka piÅ‚karska', 1, 1),
(17, 'Spodnie', 80, 98.4, 23, 'S', 'Spodnie koszykarskie', 5, 1),
(18, 'Spodnie', 90, 110.7, 23, 'S', 'Spodnie koszykarskie', 5, 6),
(19, 'Spodnie', 80, 98.4, 23, 'M', 'Spodnie koszykarskie', 5, 1),
(20, 'Spodnie', 90, 110.7, 23, 'M', 'Spodnie koszykarskie', 5, 6),
(21, 'Spodnie', 80, 98.4, 23, 'L', 'Spodnie koszykarskie', 5, 1),
(22, 'Spodnie', 90, 110.7, 23, 'L', 'Spodnie koszykarskie', 5, 6),
(23, 'PiÅ‚ka', 70, 86.1, 23, '5', 'PiÅ‚ka do gry w koszykÃ³wkÄ™', 5, 7),
(24, 'PiÅ‚ka', 80, 98.4, 23, '5', 'PiÅ‚ka do gry w koszykÃ³wkÄ™', 5, 1),
(25, 'Koszulka', 110, 135.3, 23, 'S', 'Koszulka do gry w piÅ‚kÄ™ rÄ™cznÄ…', 4, 7),
(26, 'Koszulka', 110, 135.3, 23, 'M', 'Koszulka do gry w piÅ‚kÄ™ rÄ™cznÄ…', 4, 7),
(27, 'Koszulka', 120, 147.6, 23, 'S', 'Koszulka do gry w piÅ‚kÄ™ rÄ™cznÄ…', 4, 1),
(28, 'PiÅ‚ka', 50, 61.5, 23, '5', 'PiÅ‚ka do gry w siatkÃ³wkÄ™', 6, 6),
(29, 'PiÅ‚ka', 48.5, 59.66, 23, '5', 'PiÅ‚ka do gry w siatkÃ³wkÄ™', 6, 7),
(30, 'Koszulka', 126.64, 155.77, 23, 'S', 'Koszulka do siatkÃ³wki', 6, 6),
(32, 'Pionek', 10, 10.5, 5, '3', 'Pionek do gry', 7, 9),
(33, 'DuÅ¼y pionek', 15, 15.75, 5, '5', 'DuÅ¼y pionek do gry', 7, 9),
(35, 'Spodnie', 100, 123, 23, 'M', 'Spodnie piÅ‚karskie', 1, 1),
(36, 'Koszulka', 231, 284.13, 23, 'XL', 'Koszulka piÅ‚karska', 1, 7);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stan_magazynowy`
--

CREATE TABLE `stan_magazynowy` (
  `data_stanu` date NOT NULL,
  `zasob` int(11) DEFAULT NULL,
  `produkt_nr_produktu` int(11) NOT NULL,
  `magazyn_nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `stan_magazynowy`
--

INSERT INTO `stan_magazynowy` (`data_stanu`, `zasob`, `produkt_nr_produktu`, `magazyn_nazwa`) VALUES
('2020-01-27', 1, 3, 'magazyn1'),
('2020-01-31', 2, 6, 'magazyn1'),
('2020-01-26', 0, 7, 'magazyn1'),
('2020-01-25', 6, 8, 'magazyn1'),
('2020-01-27', 1, 9, 'magazyn1'),
('2020-01-30', 4, 10, 'magazyn1'),
('2020-01-31', 3, 11, 'magazyn1'),
('2020-01-27', 4, 12, 'magazyn1'),
('2020-01-30', 4, 13, 'magazyn1'),
('2020-01-30', 2, 14, 'magazyn1'),
('2020-01-27', 0, 15, 'magazyn1'),
('2020-01-27', 2, 16, 'magazyn1'),
('2020-01-27', 0, 17, 'magazyn1'),
('2020-01-27', 0, 18, 'magazyn1'),
('2020-01-27', 0, 19, 'magazyn1'),
('2020-01-27', 0, 20, 'magazyn1'),
('2020-01-27', 0, 21, 'magazyn1'),
('2020-01-27', 0, 22, 'magazyn1'),
('2020-01-27', 0, 23, 'magazyn1'),
('2020-01-27', 0, 24, 'magazyn1'),
('2020-01-27', 0, 25, 'magazyn1'),
('2020-01-27', 0, 26, 'magazyn1'),
('2020-01-30', 3, 27, 'magazyn1'),
('2020-01-27', 8, 28, 'magazyn1'),
('2020-01-30', 5, 29, 'magazyn1'),
('2020-01-27', 5, 30, 'magazyn1'),
('2020-01-30', 4, 32, 'magazyn1'),
('2020-01-30', 0, 33, 'magazyn1'),
('2020-01-30', 0, 35, 'magazyn1'),
('2020-01-31', 0, 36, 'magazyn1'),
('2020-01-27', 0, 3, 'magazyn2'),
('2020-01-27', 0, 6, 'magazyn2'),
('2020-01-27', 0, 7, 'magazyn2'),
('2020-01-27', 0, 8, 'magazyn2'),
('2020-01-27', 0, 9, 'magazyn2'),
('2020-01-27', 0, 10, 'magazyn2'),
('2020-01-31', 84, 11, 'magazyn2'),
('2020-01-27', 0, 12, 'magazyn2'),
('2020-01-27', 0, 13, 'magazyn2'),
('2020-01-27', 0, 14, 'magazyn2'),
('2020-01-30', 1, 15, 'magazyn2'),
('2020-01-27', 0, 16, 'magazyn2'),
('2020-01-27', 0, 17, 'magazyn2'),
('2020-01-27', 0, 18, 'magazyn2'),
('2020-01-27', 0, 19, 'magazyn2'),
('2020-01-27', 0, 20, 'magazyn2'),
('2020-01-30', 5, 21, 'magazyn2'),
('2020-01-27', 0, 22, 'magazyn2'),
('2020-01-27', 0, 23, 'magazyn2'),
('2020-01-27', 0, 24, 'magazyn2'),
('2020-01-27', 0, 25, 'magazyn2'),
('2020-01-27', 0, 26, 'magazyn2'),
('2020-01-27', 0, 27, 'magazyn2'),
('2020-01-27', 0, 28, 'magazyn2'),
('2020-01-27', 0, 29, 'magazyn2'),
('2020-01-27', 0, 30, 'magazyn2'),
('2020-01-27', 1, 32, 'magazyn2'),
('2020-01-30', 0, 33, 'magazyn2'),
('2020-01-30', 0, 35, 'magazyn2'),
('2020-01-31', 0, 36, 'magazyn2'),
('2020-01-31', 0, 3, 'magazyn3'),
('2020-01-31', 0, 6, 'magazyn3'),
('2020-01-31', 0, 7, 'magazyn3'),
('2020-01-31', 0, 8, 'magazyn3'),
('2020-01-31', 0, 9, 'magazyn3'),
('2020-01-31', 0, 10, 'magazyn3'),
('2020-01-31', 15, 11, 'magazyn3'),
('2020-01-31', 0, 12, 'magazyn3'),
('2020-01-31', 0, 13, 'magazyn3'),
('2020-01-31', 0, 14, 'magazyn3'),
('2020-01-31', 0, 15, 'magazyn3'),
('2020-01-31', 0, 16, 'magazyn3'),
('2020-01-31', 0, 17, 'magazyn3'),
('2020-01-31', 0, 18, 'magazyn3'),
('2020-01-31', 0, 19, 'magazyn3'),
('2020-01-31', 0, 20, 'magazyn3'),
('2020-01-31', 0, 21, 'magazyn3'),
('2020-01-31', 0, 22, 'magazyn3'),
('2020-01-31', 0, 23, 'magazyn3'),
('2020-01-31', 0, 24, 'magazyn3'),
('2020-01-31', 0, 25, 'magazyn3'),
('2020-01-31', 0, 26, 'magazyn3'),
('2020-01-31', 0, 27, 'magazyn3'),
('2020-01-31', 0, 28, 'magazyn3'),
('2020-01-31', 0, 29, 'magazyn3'),
('2020-01-31', 0, 30, 'magazyn3'),
('2020-01-31', 0, 32, 'magazyn3'),
('2020-01-31', 0, 33, 'magazyn3'),
('2020-01-31', 0, 35, 'magazyn3'),
('2020-01-31', 0, 36, 'magazyn3');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia`
--

CREATE TABLE `zamowienia` (
  `nr_zamowienia` int(11) NOT NULL,
  `data_zlozenia_zamowienia` date NOT NULL,
  `data_przyjecia_zamowienia` date DEFAULT NULL,
  `czy_oplacone` char(1) DEFAULT NULL,
  `data_wysylki` date DEFAULT NULL,
  `czy_zrealizowane` char(1) NOT NULL,
  `data_realizacji` date DEFAULT NULL,
  `forma_platnosci` varchar(4000) NOT NULL,
  `klient_id_klienta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `zamowienia`
--

INSERT INTO `zamowienia` (`nr_zamowienia`, `data_zlozenia_zamowienia`, `data_przyjecia_zamowienia`, `czy_oplacone`, `data_wysylki`, `czy_zrealizowane`, `data_realizacji`, `forma_platnosci`, `klient_id_klienta`) VALUES
(1, '2020-01-25', '2020-01-25', 'T', '2020-01-25', 'T', '2020-01-25', 'K', 1),
(3, '2020-01-25', '2020-01-25', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 1),
(4, '2020-01-25', '2020-01-25', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 1),
(6, '2020-01-25', '2020-01-25', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 1),
(7, '2020-01-25', '2020-01-25', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 1),
(11, '2020-01-25', '2020-01-25', 'T', '2020-01-26', 'T', '2020-01-26', 'K', 1),
(18, '2020-01-26', '2020-01-26', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 1),
(19, '2020-01-26', '2020-01-26', 'T', '2020-01-27', 'T', '2020-01-27', 'G', 2),
(20, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'G', 3),
(21, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(22, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(23, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(24, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(25, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(26, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(27, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'G', 4),
(28, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'G', 4),
(29, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(30, '2020-01-27', '2020-01-27', 'T', '2020-01-27', 'T', '2020-01-27', 'K', 4),
(31, '2020-01-27', '2020-01-27', 'T', NULL, 'N', NULL, 'G', 4),
(32, '2020-01-30', '2020-01-30', 'T', '2020-01-30', 'T', '2020-01-30', 'K', 1),
(33, '2020-01-30', '2020-01-30', 'T', '2020-01-30', 'T', '2020-01-30', 'K', 1),
(34, '2020-01-30', '2020-01-30', 'T', NULL, 'N', NULL, 'K', 5),
(35, '2020-01-30', '2020-01-30', 'T', '2020-01-30', 'T', '2020-01-30', 'G', 5),
(36, '2020-01-30', '2020-01-30', 'N', NULL, 'N', NULL, 'K', 5),
(37, '2020-01-30', '2020-01-30', 'T', '2020-01-30', 'T', '2020-01-30', 'G', 5),
(38, '2020-01-30', '2020-01-30', 'N', NULL, 'N', NULL, 'K', 5),
(39, '2020-01-30', '2020-01-30', 'N', NULL, 'N', NULL, 'K', 5),
(40, '2020-01-30', '2020-01-30', 'T', '2020-01-30', 'T', '2020-01-30', 'G', 5),
(41, '2020-01-30', '2020-01-30', 'T', NULL, 'N', NULL, 'G', 1),
(42, '2020-01-30', '2020-01-30', 'T', '2020-01-30', 'T', '2020-01-30', 'G', 1),
(43, '2020-01-31', '2020-01-31', 'T', '2020-01-31', 'T', '2020-01-31', 'K', 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienie_produkt`
--

CREATE TABLE `zamowienie_produkt` (
  `ilosc` int(11) NOT NULL,
  `uwagi` varchar(4000) DEFAULT NULL,
  `zamowienia_nr_zamowienia` int(11) NOT NULL,
  `produkt_nr_produktu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `zamowienie_produkt`
--

INSERT INTO `zamowienie_produkt` (`ilosc`, `uwagi`, `zamowienia_nr_zamowienia`, `produkt_nr_produktu`) VALUES
(4, NULL, 1, 3),
(2, NULL, 1, 6),
(1, NULL, 1, 11),
(1, NULL, 3, 14),
(1, NULL, 4, 10),
(1, NULL, 6, 13),
(1, NULL, 7, 9),
(1, NULL, 7, 12),
(3, NULL, 11, 7),
(1, NULL, 18, 6),
(1, NULL, 18, 12),
(4, NULL, 19, 3),
(1, NULL, 19, 6),
(1, NULL, 19, 13),
(3, NULL, 20, 9),
(3, NULL, 20, 11),
(4, NULL, 21, 7),
(1, NULL, 21, 11),
(6, NULL, 22, 11),
(1, NULL, 23, 11),
(1, NULL, 24, 11),
(1, NULL, 25, 11),
(1, NULL, 25, 14),
(3, NULL, 26, 11),
(4, NULL, 27, 11),
(4, NULL, 27, 14),
(4, NULL, 28, 11),
(3, NULL, 28, 16),
(1, NULL, 29, 32),
(1, NULL, 30, 32),
(1, NULL, 31, 32),
(1, NULL, 32, 11),
(1, NULL, 33, 6),
(1, NULL, 33, 10),
(1, NULL, 33, 13),
(1, NULL, 33, 27),
(5, NULL, 34, 33),
(3, NULL, 35, 33),
(1, NULL, 36, 11),
(1, NULL, 37, 14),
(1, NULL, 38, 14),
(1, NULL, 39, 15),
(1, NULL, 40, 12),
(1, NULL, 41, 13),
(1, NULL, 42, 29),
(1, NULL, 42, 32),
(1, NULL, 43, 6),
(4, NULL, 43, 11);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `adres`
--
ALTER TABLE `adres`
  ADD PRIMARY KEY (`id_adresu`),
  ADD KEY `adres_klient_fk` (`klient_id_klienta`);

--
-- Indeksy dla tabeli `faktura_sprzedazy`
--
ALTER TABLE `faktura_sprzedazy`
  ADD PRIMARY KEY (`nr_faktury`),
  ADD UNIQUE KEY `faktura_sprzedazy__idx` (`zamowienia_nr_zamowienia`);

--
-- Indeksy dla tabeli `kategoria`
--
ALTER TABLE `kategoria`
  ADD PRIMARY KEY (`kategoria_id`),
  ADD UNIQUE KEY `kategoria_nazwa_un` (`nazwa_kategorii`);

--
-- Indeksy dla tabeli `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`id_klienta`);

--
-- Indeksy dla tabeli `kontakt_klient`
--
ALTER TABLE `kontakt_klient`
  ADD PRIMARY KEY (`id_kontaktu`),
  ADD KEY `kontakt_klient_klient_fk` (`klient_id_klienta`);

--
-- Indeksy dla tabeli `kontakt_pracownik`
--
ALTER TABLE `kontakt_pracownik`
  ADD PRIMARY KEY (`id_kontaktu`),
  ADD KEY `kontakt_pracownik_prac_fk` (`pracownik_id_pracownika`);

--
-- Indeksy dla tabeli `magazyn`
--
ALTER TABLE `magazyn`
  ADD PRIMARY KEY (`nazwa`);

--
-- Indeksy dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`id_pracownika`),
  ADD KEY `pracownik_magazyn_fk` (`magazyn_nazwa`);

--
-- Indeksy dla tabeli `producent`
--
ALTER TABLE `producent`
  ADD PRIMARY KEY (`producent_id`),
  ADD UNIQUE KEY `producent_nazwa_pro_un` (`nazwa_producenta`);

--
-- Indeksy dla tabeli `produkt`
--
ALTER TABLE `produkt`
  ADD PRIMARY KEY (`nr_produktu`),
  ADD KEY `produkt__idx` (`kategoria_kategoria_id`) USING BTREE,
  ADD KEY `produkt__idxv1` (`producent_producent_id`) USING BTREE;

--
-- Indeksy dla tabeli `stan_magazynowy`
--
ALTER TABLE `stan_magazynowy`
  ADD PRIMARY KEY (`magazyn_nazwa`,`produkt_nr_produktu`,`data_stanu`),
  ADD KEY `stan_magazynowy_produkt_fk` (`produkt_nr_produktu`);

--
-- Indeksy dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD PRIMARY KEY (`nr_zamowienia`),
  ADD KEY `zamowienia_klient_fk` (`klient_id_klienta`);

--
-- Indeksy dla tabeli `zamowienie_produkt`
--
ALTER TABLE `zamowienie_produkt`
  ADD PRIMARY KEY (`zamowienia_nr_zamowienia`,`produkt_nr_produktu`),
  ADD KEY `zamowienie_produkt__idx` (`produkt_nr_produktu`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `adres`
--
ALTER TABLE `adres`
  MODIFY `id_adresu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `faktura_sprzedazy`
--
ALTER TABLE `faktura_sprzedazy`
  MODIFY `nr_faktury` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT dla tabeli `kategoria`
--
ALTER TABLE `kategoria`
  MODIFY `kategoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `klient`
--
ALTER TABLE `klient`
  MODIFY `id_klienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `kontakt_klient`
--
ALTER TABLE `kontakt_klient`
  MODIFY `id_kontaktu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `kontakt_pracownik`
--
ALTER TABLE `kontakt_pracownik`
  MODIFY `id_kontaktu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `id_pracownika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `producent`
--
ALTER TABLE `producent`
  MODIFY `producent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `produkt`
--
ALTER TABLE `produkt`
  MODIFY `nr_produktu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `nr_zamowienia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `adres`
--
ALTER TABLE `adres`
  ADD CONSTRAINT `adres_klient_fk` FOREIGN KEY (`klient_id_klienta`) REFERENCES `klient` (`id_klienta`);

--
-- Ograniczenia dla tabeli `faktura_sprzedazy`
--
ALTER TABLE `faktura_sprzedazy`
  ADD CONSTRAINT `faktura_sprzedazy_zam_fk` FOREIGN KEY (`zamowienia_nr_zamowienia`) REFERENCES `zamowienia` (`nr_zamowienia`);

--
-- Ograniczenia dla tabeli `kontakt_klient`
--
ALTER TABLE `kontakt_klient`
  ADD CONSTRAINT `kontakt_klient_klient_fk` FOREIGN KEY (`klient_id_klienta`) REFERENCES `klient` (`id_klienta`);

--
-- Ograniczenia dla tabeli `kontakt_pracownik`
--
ALTER TABLE `kontakt_pracownik`
  ADD CONSTRAINT `kontakt_pracownik_prac_fk` FOREIGN KEY (`pracownik_id_pracownika`) REFERENCES `pracownik` (`id_pracownika`);

--
-- Ograniczenia dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  ADD CONSTRAINT `pracownik_magazyn_fk` FOREIGN KEY (`magazyn_nazwa`) REFERENCES `magazyn` (`nazwa`);

--
-- Ograniczenia dla tabeli `produkt`
--
ALTER TABLE `produkt`
  ADD CONSTRAINT `produkt_kategoria_fk` FOREIGN KEY (`kategoria_kategoria_id`) REFERENCES `kategoria` (`kategoria_id`),
  ADD CONSTRAINT `produkt_producent_fk` FOREIGN KEY (`producent_producent_id`) REFERENCES `producent` (`producent_id`);

--
-- Ograniczenia dla tabeli `stan_magazynowy`
--
ALTER TABLE `stan_magazynowy`
  ADD CONSTRAINT `stan_magazynowy_mag_fk` FOREIGN KEY (`magazyn_nazwa`) REFERENCES `magazyn` (`nazwa`),
  ADD CONSTRAINT `stan_magazynowy_produkt_fk` FOREIGN KEY (`produkt_nr_produktu`) REFERENCES `produkt` (`nr_produktu`);

--
-- Ograniczenia dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD CONSTRAINT `zamowienia_klient_fk` FOREIGN KEY (`klient_id_klienta`) REFERENCES `klient` (`id_klienta`);

--
-- Ograniczenia dla tabeli `zamowienie_produkt`
--
ALTER TABLE `zamowienie_produkt`
  ADD CONSTRAINT `zamowienie_produkt_fk` FOREIGN KEY (`produkt_nr_produktu`) REFERENCES `produkt` (`nr_produktu`),
  ADD CONSTRAINT `zamowienie_produkt_zam_fk` FOREIGN KEY (`zamowienia_nr_zamowienia`) REFERENCES `zamowienia` (`nr_zamowienia`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
