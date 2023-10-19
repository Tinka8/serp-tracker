# Importovat potřebné knihovny
import requests
from bs4 import BeautifulSoup
from urllib.parse import urlparse

# ----------------------------------
# Získáme odpověď z Google
# ----------------------------------

# Deklarujeme hlavičky prohlížeče, které by měl normální uživatel používat
headers = {'User-Agent':'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36','referer':'https://www.google.com'}

# Definujeme URL adresu, na které chceme výsledky hledat (Google)
target_url = 'https://www.google.com/search?q=zastav%C3%A1rna+B%C3%ADlina'

# Získáme odpověď z Google
response = requests.get(target_url, headers=headers)

# Vypíšeme status code odpovědi
print(response.status_code)

# Vyhodnotíme jestli požadavek skončil úspěchem
if (response.status_code == 200):
    print("Vše v pořádku")
else:
    print("Něco se pokazilo")

# ----------------------------------
# Analyzujeme odpověď
# ----------------------------------

# Doména, kterou chceme ve výsledcích vyhledat
search_for_domain = "www.zastavarna-bilina.cz"

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

# Vyhledávací výraz, který jsme zadali
search_for_phrase = "zastavárna Bílina"

# We found the domain we are looking for
if(found == True):
    print(f"Vyhledávaná stránka { search_for_domain } byla pro vyhledávací výraz { search_for_phrase } nalezena na pozici { position }")

# We did not find the domain we are looking for
else:
    print(f"Vyhledávaná stránka nebyla nalezena v prvních { len(results) }")

