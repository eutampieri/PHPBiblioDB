import urllib
from lxml import html
from sys import argv
isbn = argv[1]
xpath1 = '//*[@id="main"]/div[2]/div[1]/div[2]/h2/a/@href'
search = urllib.urlopen(
    'http://www.scuolabook.it/catalogsearch/result/?q='+isbn).read()
tree = html.fromstring(search)
linkd = tree.xpath(xpath1)[0]
desc = urllib.urlopen(linkd).read()
img = html.fromstring(desc).xpath('//*[@id="main"]/div[1]/div[1]/img/@src')[0]
print(img)
