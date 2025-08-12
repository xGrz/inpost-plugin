# InPost Plugin for Laravel

## Dlaczego pakiety `require-dev` nie są zainstalowane?

To zachowanie wynika z działania Composera:

- Zależności z sekcji `require-dev` są instalowane tylko dla **pakietu‑głównego (root project)**, czyli wtedy, gdy uruchamiasz `composer install` w katalogu tego pakietu.
- Jeżeli ten pakiet jest używany jako **zależność innego projektu** (np. Twojej aplikacji Laravel, dodany jako repozytorium `path`, VCS lub z Packagista), to jego `require-dev` **nie są instalowane** przez Composera. To jest zamierzone – deweloperskie zależności pakietu nie powinny „przeciekać” do projektu nadrzędnego.
- Dodatkowo `require-dev` nie zostaną zainstalowane, jeśli używasz flagi `--no-dev` lub masz ustawioną zmienną środowiskową `COMPOSER_NO_DEV=1`.

## Jak zainstalować `require-dev` dla pracy nad tym pakietem?

Jeśli chcesz rozwijać ten pakiet lokalnie, wejdź do jego katalogu i zainstaluj zależności deweloperskie jako pakiet‑główny:

```bash
cd packages/inpost
composer install    # domyślnie z dev
# lub równoważnie z dowolnego miejsca:
composer install --working-dir=packages/inpost
```

Upewnij się, że nie używasz `--no-dev` i że nie masz ustawionego `COMPOSER_NO_DEV=1`.

## Uruchamianie testów

W repozytorium dostępny jest skrypt:

```bash
composer test
```

Domyślnie uruchamia on PHPUnit z konfiguracją `phpunit.xml.dist` i wykorzystuje `orchestra/testbench` do bootstrapa środowiska Laravel.

## Typowe przyczyny braku `require-dev`

- Uruchamiasz `composer install` w projekcie nadrzędnym (aplikacji), a nie w katalogu pakietu – w takim wypadku `require-dev` pakietu nie są brane pod uwagę.
- Użyto `composer install --no-dev` lub ustawiono `COMPOSER_NO_DEV=1` (np. w CI/produkcyjnym środowisku).
- Cache Composera/lock powoduje pominięcie zmian – rozważ `composer update` (w razie potrzeby) w katalogu pakietu.

## Informacje o pakiecie

- Nazwa: `xgrz/inpost-plugin`
- Wymaga: PHP ^8.2, Laravel ^10.0|^11.0|^12.0
- Dev: `phpunit/phpunit` (^10.5|^11.5), `orchestra/testbench` (^8|^9|^10)

Jeśli nadal masz pytania lub coś nie działa, sprawdź logi Composera z przełącznikiem `-vvv` lub opisz swój scenariusz (gdzie uruchamiasz `composer`, w jakim katalogu i z jakimi flagami).
