****************************************
■組み込みのPHP言語機能を無効化
****************************************

「@builtin php」で検索

「PHP 言語機能」を無効

****************************************
■Visual Studio Code拡張機能
****************************************

・Japanese Language Pack for Visual Studio Code

・vscode-icons

・WSL

・Docker

・Dev Containers

・Container Tools

・PHP Intelephense

・Path Autocomplete

・MySQL

****************************************
■WSLとコンテナ操作
****************************************

【WSLログイン】

・ターミナルから「wsl」でログイン

・wslのフォルダを開く
$ code /home/developer/develop/sandbox/src/base

・ターミナルからコンテナ起動
$ docker-compose up --build
$ docker-compose up

【コンテナログイン】

・WSLログイン状態で「Ctrl + Shift + P」でコマンドパレットから「Dev Containers: Rebuild Container」を選択

【WSLとコンテナの2ウィンドウ】

・再度ターミナルから「wsl」でログイン

・再度wslのフォルダを開く
$ code /home/developer/develop/sandbox/src/base

****************************************
■Xdebug
****************************************

・明示的にリクエストを毎回送る

【ブラウザ】
http://localhost:8000/?XDEBUG_SESSION=1

【CLI】
$ XDEBUG_SESSION=1 php artisan migrate
