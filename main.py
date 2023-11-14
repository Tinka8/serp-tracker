# Importovat potřebné knihovny
from argparse import ArgumentParser
from urllib.parse import urlparse
import requests
from bs4 import BeautifulSoup
import pymysql

# ----------------------------------
# Konfigurace
# ----------------------------------

parser = ArgumentParser()
parser.add_argument("-d", "--domain", dest="domain", help="Domain we are looking for", default="www.zastavarna-bilina.cz")
parser.add_argument("-s", "--search", dest="search", help="Search phrase", default="zastavárna Bílina")
parser.add_argument("-n", "--number", dest="number", help="Number of results", default=100)
args = parser.parse_args()

# Doména, kterou chceme ve výsledcích vyhledat
search_for_domain = args.domain

# Vyhledávací výraz, který jsme zadali
search_for_phrase = args.search

# Maximální počet výsledků, které chceme získat
search_max_results = args.number

# Maximální počet výsledků, které chceme získat
search_max_results = 100

# Výchozí hodnota je nenalezeno
found = False

# ----------------------------------
# Získáme odpověď z Google
# ----------------------------------

# Deklarujeme hlavičky prohlížeče, které by měl normální uživatel používat
headers={'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36','referer':'https://www.google.com'}

# Definujeme URL adresu, na které chceme výsledky hledat (Google)
target_url='https://www.google.com/search?q=' + search_for_phrase + '&num=' + str(search_max_results)

# Získáme odpověď z Google
response = requests.get(target_url, headers=headers)

# Vypíšeme status code odpovědi
print(response.status_code)

# ----------------------------------
# Analyzujeme odpověď
# ----------------------------------

# Parsování odpovědi pomocí knihovny BeautifulSoup
soup = BeautifulSoup(response.text,'html.parser')

# Vytvoříme pole, do kterého budeme ukládat výsledky
results = soup.find_all("div", class_="MjjYud")

# Budeme iterovat všemi nalezenými výsledky
for result in range(0, len(results)):

    # Vytvoříme proměnnou, do které budeme ukládat hlavičku výsledku
    resultHeader = results[result].find("div", class_="yuRUbf")
    
    # Pokud hlavička není nalezena, pokračujeme na další výsledek
    if (resultHeader == None):
        continue

    # Z hlavičky výsledku získáme doménu
    domain = urlparse(resultHeader.find("a").get("href")).netloc
    # print(domain)

    # Porovnáme doménu s doménou, kterou hledáme
    # Pokud se domény shodují, nastavíme proměnnou found a skončíme s iterací
    # Pokud se domény neshodují, nastavíme proměnnou found na False
    if(domain == search_for_domain):
        found = True
        position = result + 1
        break
    else:
        found = False

# ----------------------------------
# Výsledky analýzy
# ----------------------------------

# Našli jsme doménu, kterou hledáme
if(found == True):
    print("Vyhledávaná stránka", search_for_domain, "byla pro vyhledávací výraz", search_for_phrase, "nalezena na pozici", position)

# Nenašli jsme doménu, kterou hledáme
else:
    print("Vyhledávaná stránka nebyla nalezena v prvních", len(results))
    exit(1)

# ----------------------------------
# Zaznamenat výsledky do databáze
# ----------------------------------

# Připojení k databázi
connection = pymysql.connect(
    host='localhost',
    user='root',
    password='matejvolesini',
    database='serp_tracker'
)

# Otevření kurzoru
cursor = connection.cursor()

# Vložení dat do databáze
sql = "INSERT INTO serp_results (search_for_domain, search_for_phrase, position) VALUES (%s, %s, %s)"
cursor.execute(sql, (search_for_domain, search_for_phrase, position))

# Commit změn
connection.commit()

# Uzavření spojení
cursor.close()
connection.close()
