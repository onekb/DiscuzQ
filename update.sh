export http_proxy=http://127.0.0.1:1087;export https_proxy=http://127.0.0.1:1087;
git pull
composer create-project --prefer-dist qcloud/discuz:v2.3.210208 -vvv --repository=https://cloud.discuz.chat discuzQ-github
curl https://cloud.discuz.chat/dist/discuz/core/discuz-core-v2.3.210208-de8c6a.zip -o core.zip
unzip -o -d ./discuzQ-github/framework core.zip
cd ./discuzQ-github
composer install -vvv
cp -R ../.git/. ./.git
git add .
# git commit -m 'Discuz! Q RC v2.3.210207'
git commit -m 'Discuz! Q ALPHA v2.3.210208'
git push -f
cd ..
rm -rf discuzQ-github core.zip