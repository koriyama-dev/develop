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

・PHP Debug

・MySQL

・Windsurf Plugin (formerly Codeium)

****************************************
■WSLとコンテナ操作
****************************************

【WSLログイン】

・ターミナルから「wsl」でログイン

・wslのフォルダを開く
$ export SANDBOX_NAME=sandbox1
code /home/developer/develop/src

または

$ export SANDBOX_NAME=sandbox2
code /home/developer/develop/src

・ターミナルからコンテナ起動
$ docker-compose up --build
or
$ docker-compose up

【コンテナログイン】

・WSLログイン状態で「Ctrl + Shift + P」でコマンドパレットから
DockerFileや設定ファイルの変更時：「Dev Containers: Rebuild Container」を選択
次回以降で設定の変更が無い場合  ：「Dev Containers: Reopen in Container」を選択

【WSLとコンテナの2ウィンドウ】

・再度ターミナルから「wsl」でログイン

・再度wslのフォルダを開く
$ export SANDBOX_NAME=sandbox1
code /home/developer/develop/src

または

$ export SANDBOX_NAME=sandbox2
code /home/developer/develop/src

****************************************
■Xdebug
****************************************

・明示的にリクエストを毎回送る

【ブラウザ】
https://base.local/?XDEBUG_SESSION=1

【CLI】
$ XDEBUG_SESSION=1 php artisan migrate
