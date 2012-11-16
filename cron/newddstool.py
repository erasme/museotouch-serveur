#!/usr/bin/env python
'''
Usage: dxttool <options> filename

    -c, --compress          Compress an image to DXT
    -d, --decompress        Decompress an image from DXT to PNG
    -o, --output filename   Change the output filename
    -h, --help              Show this usage

Compress an image:
    dxttool -c <filename.png>

Decompress an image:
    dxttool -d <filename.dxt>

Note: Decompression will always output an png image
Note: Compression accept PNG, JPEG, GIF, TGA...
'''

import sys
from sys import exit, argv, stdout
import getopt
import pygame
import squish
import struct

def usage():
    print __doc__

def trace(msg):
    stdout.write(msg)
    stdout.flush()

def _nearest_pow2(v):
    # From http://graphics.stanford.edu/~seander/bithacks.html#RoundUpPowerOf2
    # Credit: Sean Anderson
    v -= 1
    v |= v >> 1
    v |= v >> 2
    v |= v >> 4
    v |= v >> 8
    v |= v >> 16
    # return v + 1
    return v - (v >> 1)

if __name__ == '__main__':

    try:
        opts, args = getopt.getopt(argv[1:], 'hcdo:',
                ['help', 'compress', 'decompress', 'output='])
    except getopt.GetoptError, err:
        print str(err)
        usage()
        exit(1)

    sys.argv = sys.argv[:1]

    command = None
    output = None
    for o, a in opts:
        if o in ('-c', '--compress'):
            command = 'compress'
        elif o in ('-d', '--decompress'):
            command = 'decompress'
        elif o in ('-h', '--help'):
            usage()
            exit(0)
        elif o in ('-o', '--output'):
            output = a
        else:
            assert False, 'unhandled option'


    if command is None:
        usage()
        exit(1)

    if len(args) != 1:
        usage()
        exit(1)

    fn = args[0]

    #pygame.display.init()

    if command == 'compress':

        from kivy.lib.ddsfile import DDSFile

        if output is None:
            output = fn.rsplit('.', 1)[0] + '.dds'

        img = pygame.image.load(fn)
        print 'Original image is', img.get_size()
        #img = img.subsurface(img.get_bounding_rect())
        width, height = img.get_size()

        # print 'Output image is ', width, height, '\n'
        #print 'Bounding box found is', img.get_size()

        # ensure RGBA
        if img.get_bytesize() != 4:
            pass #img = img.convert(32)

        # create container
        dds = DDSFile()

        # all the job:
        level = 0
        trace(' %d(%d,%d)' % (level, width, height))
        data = pygame.image.tostring(img, 'RGBA', True)
        data = squish.compressImage(data, width, height,
            squish.DXT1| squish.COLOR_ITERATIVE_CLUSTER_FIT |
            squish.WEIGHT_COLOR_BY_ALPHA)
        dds.add_image(level, 32, 'dxt1', width, height, data)
        dds.save(output)
        trace('Done !\n')

