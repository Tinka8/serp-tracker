# cSpell:disable
# ----------------------------------------------------------------------
#
# file name: main.py
# project: SERP Tracker
#
# usage:
#
#   python main.py -d www.zastavarna-bilina.cz -s "zastavárna Bílina" -n 100
#   python main.py -a
#
# help:
#
#   python main.py -h
# ----------------------------------------------------------------------
# Importovat potřebné knihovny
from argparse import ArgumentParser
from urllib.parse import urlparse
import time
import requests
from bs4 import BeautifulSoup
import pymysql

start_time = time.time()

# ----------------------------------
# Připojení k databázi
# ----------------------------------

# Připojení k databázi
connection = pymysql.connect(
    host="localhost", 
    user="root", 
    password="", 
    database="serp_tracker"
)

# ----------------------------------
# Konfigurace
# ----------------------------------

parser = ArgumentParser()
parser.add_argument(
    "-d",
    "--domain",
    dest="domain",
    help="Domain we are looking for",
    default="www.zastavarna-bilina.cz",
)
parser.add_argument(
    "-s", "--search", dest="search", help="Search phrase", default="zastavárna Bílina"
)
parser.add_argument(
    "-n", "--number", dest="number", help="Number of results", default=100
)
parser.add_argument(
    "-a",
    "--all",
    dest="all",
    action="store_true",
    help="Scrape all from serp_presets",
    default=False,
)
parser.add_argument(
    "-v",
    "--verbose",
    dest="verbose",
    action="store_true",
    help="Verbose mode",
    default=False,
)
args = parser.parse_args()

# ----------------------------------
# Získání pozice
# ----------------------------------


def get_position(get_position_domain, get_position_phrase, get_position_max_results):
    # ----------------------------------
    # Získáme odpověď z Google
    # ----------------------------------

    # Deklarujeme hlavičky prohlížeče, které by měl normální uživatel používat
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36",
        "referer": "https://www.google.com",
    }

    # Definujeme URL adresu, na které chceme výsledky hledat (Google)
    target_url = (
        "https://www.google.com/search?q="
        + get_position_phrase
        + "&num="
        + str(get_position_max_results)
    )

    # Získáme odpověď z Google
    response = requests.get(target_url, headers=headers)

    # ----------------------------------
    # Analyzujeme odpověď
    # ----------------------------------

    # Parsování odpovědi pomocí knihovny BeautifulSoup
    soup = BeautifulSoup(response.text, "html.parser")

    # Vytvoříme pole, do kterého budeme ukládat výsledky
    results = soup.find_all("div", class_="MjjYud")

    # Tady si uložíme, na které pozici byla nalezená doména
    found_position = 0

    # Budeme iterovat všemi nalezenými výsledky
    for result in range(0, len(results)):
        # Vytvoříme proměnnou, do které budeme ukládat hlavičku výsledku
        resultHeader = results[result].find("div", class_="yuRUbf")

        # Pokud hlavička není nalezena, pokračujeme na další výsledek
        if resultHeader == None:
            continue

        # Z hlavičky výsledku získáme doménu
        domain = urlparse(resultHeader.find("a").get("href")).netloc
        # print(domain)

        # Porovnáme doménu s doménou, kterou hledáme
        # Pokud se domény shodují, nastavíme proměnnou found a skončíme s iterací
        # Pokud se domény neshodují, nastavíme proměnnou found na False
        if domain == get_position_domain:
            found_position = result + 1
            break

    return found_position


# ----------------------------------
# Výsledky analýzy
# ----------------------------------


def print_position_results(
    local_domain, local_phrase, found_position, used_max_results
):
    found = True if found_position > 0 else False

    # Našli jsme doménu, kterou hledáme
    if found == True:
        print(
            "Vyhledávaná stránka",
            local_domain,
            "byla pro vyhledávací výraz",
            local_phrase,
            "nalezena na pozici",
            found_position,
        )

    # Nenašli jsme doménu, kterou hledáme
    else:
        print("Vyhledávaná stránka nebyla nalezena v prvních", used_max_results)


# ----------------------------------
# Zaznamenat výsledky do databáze
# ----------------------------------


def write_position_results(used_domain, used_phrase, found_position):
    # Otevření kurzoru
    cursor = connection.cursor()

    # Vložení dat do databáze
    sql = "INSERT INTO serp_results (search_for_domain, search_for_phrase, position) VALUES (%s, %s, %s)"
    cursor.execute(sql, (used_domain, used_phrase, found_position))

    # Commit změn
    connection.commit()

    # Uzavření spojení
    cursor.close()


# ----------------------------------
# Spouštění čtení a zapsání výsledků
# ----------------------------------


def load_position(local_domain, local_phrase, local_max_results, debug=False):
    if debug:
        print("Hledáme doménu", local_domain, "pro výraz", local_phrase)

    position = get_position(local_domain, local_phrase, local_max_results)

    if debug:
        print("Pozice je", position)

    print_position_results(local_domain, local_phrase, position, local_max_results)

    if position > 0:
        write_position_results(local_domain, local_phrase, position)

        if debug:
            print("Výsledky byly zapsány do databáze")


def load_all_positions(local_max_results, debug=False):
    # Get all presets from table serp_presets
    cursor = connection.cursor()

    sql = "SELECT search_for_domain, search_for_phrase FROM serp_presets"

    cursor.execute(sql)

    presets = cursor.fetchall()

    cursor.close()

    # Iterate over presets
    for preset in presets:
        load_position(preset[0], preset[1], local_max_results, debug)


# ----------------------------------
# Spustit analýzu
# ----------------------------------

# Doména, kterou chceme ve výsledcích vyhledat
search_for_domain = args.domain

# Vyhledávací výraz, který jsme zadali
search_for_phrase = args.search

# Maximální počet výsledků, které chceme získat
search_max_results = args.number

# Chceme provést vyhledání všech uložených pozic
search_all = args.all

# Chceme použít verbose mód
verbose = args.verbose

# Spustíme analýzu
if search_all:
    load_all_positions(search_max_results, verbose)
else:
    load_position(search_for_domain, search_for_phrase, search_max_results, verbose)


# ----------------------------------
# Odpojení od databáze
# ----------------------------------

connection.close()

end_time = time.time()

elapsed_time = end_time - start_time
print("Skript běžel", elapsed_time, "sekund.")