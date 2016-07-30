import cv2
import os
import json

files_to_process = os.listdir('../raw_images')

files_json = []

image_count = 0

for current_filename in files_to_process:
    image_count = image_count + 1
    image_original = cv2.imread('../raw_images/' + current_filename)
    current_filename = current_filename[0:-4]
    hist = cv2.calcHist([image_original], [0], None, [256], [0, 256])
    brightness = 0
    for i in range(0, 255):
        brightness = brightness + i * hist[i, 0]
    brightness = brightness / image_original.shape[0] / image_original.shape[1]
    files_json = files_json + [ { current_filename: { 'brightness': brightness } }]
    if image_count % 100 == 0:
        print(image_count)

with open('../www/data/features.json', 'w') as jsonfile:
    json.dump(files_json, jsonfile)