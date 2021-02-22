setproxy
git pull
composer create-project --prefer-dist qcloud/discuz:v2.3.210207 -vvv --repository=https://cloud.discuz.chat discuzQ-github
curl https://cloud.discuz.chat/dist/discuz/core/discuz-core-v2.3.210207-a5da56.zip -o core.zip
unzip -o -d ./discuzQ-github/framework core.zip
cd ./discuzQ-github
composer install -vvv
cp -R ../.git/. ./.git
git add .
git commit -m 'Discuz! Q RC v2.3.210207'
# git commit -m 'Discuz! Q ALPHA v2.2.210125'
git push -f
cd ..
rm -rf discuzQ-github core.zip