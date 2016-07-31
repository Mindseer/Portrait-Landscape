import cv2
import os
import json
import lxml.etree

files_to_process = os.listdir('../raw_images')

files_json = []

image_count = 0

parser = lxml.etree.XMLParser(recover=True)
tree = lxml.etree.parse('portraitau-20160705.xml', parser);

for portrait in tree.iter('portrait'):
    for image in portrait.iter('image'):
        current_filename = portrait.get("irn") + "_" + image.get("irn") + ".jpg"
        if os.path.isfile("../raw_images/" + current_filename):
            image_count = image_count + 1
            image_original = cv2.imread('../raw_images/' + current_filename, 0)
            current_filename = current_filename[0:-4]
            brightness = image_original.sum() / image_original.size
            file_json = {'id': current_filename, 'brightness': brightness}
            for date_created in portrait.iter('datecreated'):
                file_json["date_created"] = date_created.text
            for date_created in portrait.iter('media'):
                file_json["media"] = date_created.text
            files_json = files_json + [file_json]
            if image_count % 100 == 0:
                print(image_count)

with open('../www/data/features.json', 'w') as jsonfile:
    json.dump(files_json, jsonfile)