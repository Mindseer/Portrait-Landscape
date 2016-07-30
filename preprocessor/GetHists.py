import cv2

img = cv2.imread('C:/Users/max/Desktop/GovHack/Portraits/1_5120.jpg', 0)
print(cv2.calcHist([img], [0], None, [256], [0, 256]))
