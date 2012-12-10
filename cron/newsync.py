#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import subprocess
import time

from pdb import set_trace as rrr

AGE_MAXIMUM = 10 # en secondes

def execute(cmd):
	output, error = subprocess.Popen(
		cmd.split(' '), 
		stdout=subprocess.PIPE, 
		stderr=subprocess.PIPE
	).communicate()
	return {'out':output, 'err':error}

def prefix(f):
	return f.rsplit('.', 1)[0]

def suffix(f):
	return f.rsplit('.', 1)[1]

def recent(f):
	return time.time() - os.path.getmtime(f) < AGE_MAXIMUM

def who_needs_convert(root, raw):
	return [f for f in raw if suffix(f).lower() in ['png', 'jpg', 'jpeg', 'gif', 'bmp'] and recent(os.path.join(root, f))]

aa = [e for e in os.walk('/home/www/museotouch/uploads/objets/')]
tup_raw = [e for e in aa if e[0].endswith('/raw')]
tup_dds = [e for e in aa if e[0].endswith('/dds')]
for raw, dds in zip(tup_raw, tup_dds):
	print '# raw dir:', raw[0]
	print '# dds dir:', dds[0]
	raw_files = raw[2]
	dds_files = dds[2]
	need_convert = who_needs_convert(raw[0], raw_files)
	for f in need_convert:
		input_path = os.path.join(raw[0], f)
		ddsname = prefix(f) + '.dds'
		output_path = os.path.join(dds[0], ddsname)
		print '>>> Convert', input_path, 'to', output_path
		r = execute('/home/www/museotouch/cron/newddstool.py -o {dds} -c {png}'.format(png=input_path, dds=output_path))
		print r['out']
		print r['err']
	print
