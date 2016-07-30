import cv2
import math
import os
import numpy
import json

max_width = 32
max_height = 48
half_width = math.ceil(max_width / 2)
half_height = math.ceil(max_height / 2)
max_ratio = max_height / max_width
elements = 2261
columns = 50
rows = math.ceil(elements / columns)
sheet_width = max_width * columns
sheet_height = max_height * rows

spritesheet_image = numpy.zeros((sheet_height, sheet_width, 4), numpy.uint8)
image_count = -1

spritesheet_json = {"frames":{},"meta":{"image":"sprites.png","format": "RGBA8888","size": {"w":sheet_width,"h":sheet_height},"scale": "1"}}

files_to_process = os.listdir('../raw_images')
for current_filename in files_to_process:
    image_count = image_count + 1
    image_original = cv2.imread('../raw_images/' + current_filename)
    current_filename = current_filename[:-4]

    if image_original.shape[0] / image_original.shape[1] > max_ratio:
        img_small = cv2.resize(image_original, (math.floor(max_height * image_original.shape[1] / image_original.shape[0]), max_height))
    else:
        img_small = cv2.resize(image_original, (max_width, math.floor(max_width * image_original.shape[0] / image_original.shape[1])))

    top_left = (max_height * math.floor(image_count / columns) + half_height - math.floor(img_small.shape[0] / 2), max_width * (image_count % columns) + half_width - math.floor(img_small.shape[1] / 2))
    spritesheet_json["frames"][current_filename] = {}
    spritesheet_json["frames"][current_filename]["frame"] = {"x": top_left[1], "y": top_left[0], "w": img_small.shape[1], "h": img_small.shape[0]}

    #spritesheet_json["frame", current_filename, "rotated"] = false
    #spritesheet_json["frame", current_filename, "trimmed"] = false
    spritesheet_json["frames"][current_filename]["spriteSourceSize"] = {"x": 0, "y": 0, "w": img_small.shape[1], "h": img_small.shape[0]},
    spritesheet_json["frames"][current_filename]["sourceSize"] = {"w": img_small.shape[1], "h": img_small.shape[0]}
    spritesheet_image[top_left[0]:top_left[0] + img_small.shape[0], top_left[1]:top_left[1] + img_small.shape[1]] = cv2.cvtColor(img_small, cv2.COLOR_BGR2BGRA)
cv2.imwrite('../www/data/images/sprites.png', spritesheet_image)
with open('../www/data/images/sprites.json', 'w') as jsonfile:
    json.dump(spritesheet_json, jsonfile)