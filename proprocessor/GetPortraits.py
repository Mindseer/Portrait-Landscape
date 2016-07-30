import lxml.etree
import urllib.request

parser = lxml.etree.XMLParser(recover=True)
tree = lxml.etree.parse('portraitau-20160705.xml', parser);

whereleftoff = 0
icount = 0

for portrait in tree.iter('portrait'):
  icount = icount + 1
  for image in portrait.iter('image'):
    for url in image.iter('fileURL'):
      print(url.text)
      if portrait.get('irn') == '9392':
        whereleftoff = 1
print(icount)