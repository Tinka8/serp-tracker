# SERP

SERP, or Search Engine Results Page, is a critical element in the world of online search and digital marketing.
It refers to the page that a search engine, such as Google, Bing, or Yahoo, displays after a user enters a query into the search bar.

##

This page lists a variety of results in response to the search query and plays a pivotal role in determining which websites or web pages users visit.

## Key aspects of SERPs:

- Organic Results: The primary content on a SERP is typically made up of organic search results. These are web pages that the search engine's algorithm deems most relevant to the user's query. Search engine optimization (SEO) is the practice of optimizing web content to improve its ranking in these organic search results.

- Paid Results: In addition to organic results, SERPs may also feature paid advertisements. These are usually labeled as "Ad" or "Sponsored". Advertisers bid on specific keywords to have their ads displayed when those keywords are used in search queries.

- Knowledge Graphs: Search engines often include knowledge graphs on SERPs, which provide information about a subject, person, place, or event directly in the search results. Knowledge graphs are designed to enhance the user's search experience by providing quick, easily digestible information.

- Local Results: For location-based queries, SERPs often include a map with local businesses, along with information like contact details, reviews, and hours of operation. These results are crucial for users seeking nearby products or services.

- Related Searches: At the bottom of the SERP, search engines may suggest related queries to help users refine their search or explore related topics

## Installation

```sh
pip install requests
pip install beautifulsoup4
pip install PyMySQL
```

## Installation DB

### Create database

```sql
CREATE DATABASE serp_tracker;
USE `serp_tracker`;
```

### Migrate tables

```sql
# serp_results
CREATE TABLE `serp_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_for_domain` varchar(255) DEFAULT NULL,
  `search_for_phrase` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `current_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# serp_presets
CREATE TABLE `serp_presets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_for_domain` varchar(255) DEFAULT NULL,
  `search_for_phrase` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

## Usage

### Run script for specific domain and phrase

```sh
python main.py -d www.zastavarna-bilina.cz -s "zastavárna Bílina" -n 100
```

### Run all preset domains and phrases

```sh
python -a
```

### Arguments and options

| Argument | Name      | Default                    | Description                                       |
| -------- | --------- | -------------------------- | ------------------------------------------------- |
| -d       | --domain  | `www.zastavarna-bilina.cz` | Domain we are looking for                         |
| -s       | --search  | `zastavárna Bílina`        | Search phrase                                     |
| -n       | --number  | 100                        | Maximum number of results to check                |
| -a       | --all     | `false`                    | Scrape all from serp_presets, overrides -d and -s |
| -v       | --verbose | `false`                    | Verbose mode                                      |

---
