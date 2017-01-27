import urllib2,lxml
from sys import argv
#request = urllib2.Request('http://coopreno.deliverybooks.it/Search?q='+argv[1]+"&adv=no")
request = urllib2.Request('http://coopreno.deliverybooks.it/'+argv[1])
request.add_header('User-Agent', '"Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:47.0) Gecko/20100101 Firefox/47.0"')
opener = urllib2.build_opener(urllib2.HTTPCookieProcessor())
f = opener.open(request)
print f.url
lid=f.url.replace("http://coopreno.deliverybooks.it/","").split("/")[1]
print "http://coopreno.deliverybooks.it/cover_images/"+lid[:3]+"/"+lid+".396.jpg"
