from crontab import CronTab
from collections import defaultdict
from datetime import datetime
import argparse

TIME_UNITS = ['minute', 'hour', 'day', 'month', 'day_of_week']

parser = argparse.ArgumentParser()

group = parser.add_mutually_exclusive_group()
group.add_argument(
	'-a', '--add', action='store_true',
	help='adds a new job, depends on the time modifier options'
	)
group.add_argument(
	'-e', '--edit', action='store_true',
	help='edits an existing job, depends on the find option'
	)
group.add_argument(
	'-r', '--remove', action='store_true',
	help='removes an existing job, depends on the find option'
	)
group.add_argument(
	'-ls', '--list', action='store_true',
	help='lists available jobs, depends on the find option'
	)
group.add_argument(
	'-l', '--log', action='store_true',
	help='lists available logs for an existing job, depends on the find option'
	)
group.add_argument(
	'-s', '--schedule',
	help='lists the schedule of an existing job, depends on the find option \
	      arguments: {nav} {n}, nav can be either be n/next or p/prev, \
	      n is the number of records to be shown. e.g. -s "n 5"'
	)
group.add_argument(
	'-fq', '--freq',
	help='prints the number of times per day or year the job will run, arguments: d/day or y/year'
	)

parser.add_argument(
	'-f', '--find',
	help='find an existing job with a command, comment, or time, \
	      arguments: {cmd} {val}, cmd can be c/cmd/command or cm/cmnt/comment or t/time, \
	      val is the string value to search with'
	)
parser.add_argument(
	'-mn', '--minute',
	help='time modifier controlling minutes, args: {mod} {val}, mod takes a value of d/during o/on e/every \
	      val takes a value 0-59, can be a range for `during`, used in combination with other time and action options'
	)
parser.add_argument(
	'-hr', '--hour',
	help='time modifier controlling hours, args: {mod} {val}, mod takes a value of d/during o/on e/every \
	      val takes a value 0-23, used in combination with other time and action options'
	)
parser.add_argument(
	'-d', '--day',
	help='time modifier controlling days, args: {mod} {val}, mod takes a value of d/during o/on e/every \
	      val takes a value 1-31, used in combination with other time and action options'
	)
parser.add_argument(
	'-m', '--month',
	help='time modifier controlling month, args: {mod} {val}, mod takes a value of d/during o/on e/every \
	      val takes a value 1-12 or JAN-DEC, used in combination with other time and action options'
	)
parser.add_argument(
	'-dw', '--day-of-week',
	help='time modifier controlling day of the week, args: {mod} {val}, mod takes a value of d/during o/on e/every \
	      val takes a value 0-6 or SAT-SUN, used in combination with other time and action options'
	)
parser.add_argument(
	'-c', '--command',
	help='sets the command as executed on a command line for the job'
	)
parser.add_argument(
	'-cm', '--comment',
	help='sets a comment for the job'
	)
parser.add_argument(
	'-u', '--user', default='root',
	help='sets the user for the cron file'
	)

args = parser.parse_args()

user = args.user or 'root'
cron = CronTab(user=user, log='/var/log/syslog')

def parse_duration(cmds):
	cmds = cmds.replace('also', '&').split('&')
	durs = []
	for cmd in cmds:
		tokens = cmd.split()
		dur = defaultdict()
		for idx, _ in enumerate(tokens):
			if idx%2==0:
				key = tokens[idx]
				value = tokens[idx+1]

				key = key.replace('d', 'during')
				key = key.replace('e', 'every')
				key = key.replace('on', 'o').replace('o', 'on')
				if key == 'during':
					value = value.replace('-', ',').split(',')

				dur[key] = value
		durs.append(dur)
	return durs

def apply_duration(job, unit, durs):
	call_chain = getattr(job, unit)
	for dur in durs:
		for key, value in dur.items():
			if type(value) == list:
				call_chain = getattr(call_chain, key)(*value)
			else:
				call_chain = getattr(call_chain, key)(value)
		call_chain = getattr(job, unit).also
	return job

def apply_unit_durations(job):
	for unit in TIME_UNITS:
		arg_unit = getattr(args, unit)
		if arg_unit:
			durs = parse_duration(arg_unit)
			if unit == 'day_of_week': unit = 'dow'
			job = apply_duration(job, unit, durs)
	return job

def add(cmd=args.command, cmnt=args.comment):
	job = cron.new(command=cmd, comment=cmnt)
	job = apply_unit_durations(job)
	cron.write()
	print 'Job added ' + job.render()

def find(arg=args.find):
	if not arg:
		print 'Please use the find option to select jobs to manipulate'
		return

	cmd, value = arg.split()

	if cmd == 'command' or cmd == 'cmd' or cmd == 'c':
		jobs = cron.find_command(value)
	if cmd == 'time' or cmd == 't':
		jobs = cron.find_time(value)
	if cmd == 'comment' or cmd == 'cmnt' or cmd == 'cm':
		jobs = cron.find_comment(value)

	jobs = [job for job in jobs]
	return jobs

def edit(cmd=args.command, cmnt=args.comment):
	jobs = find()
	if len(jobs) != 1: print 'Narrow your criteria a bit for edits'
	job = jobs[0]

	if cmd: job.set_command(cmd)
	if cmnt: job.set_comment(cmnt)

	time = False
	for unit in TIME_UNITS:
		if getattr(args, unit): time = True
	if time: apply_unit_durations(job)

	cron.write()
	print 'Job edited ' + job.render()

def remove():
	jobs = find()
	for job in jobs:
		print 'Removing job ' + job.render()
		cron.remove(job)
	cron.write()
	print 'Removed Successfully'

def list():
	jobs = find() if args.find else cron
	for job in jobs:
		print job

def log():
	jobs = find()
	for job in jobs:
		for entry in job.log:
			print entry['date'].strftime('%Y-%m-%d %I:%M %p') +' - '+ \
			      entry['cmd'] +' - '+ \
			      entry['user'] + '\n'

def schedule(arg=args.schedule):
	if not arg:
		print 'Please supply option arguments'

	arg = arg.split()
	cmd = arg[0]
	value = arg[1] if len(arg) is 2 else 1

	nav = ''
	if cmd == 'next' or cmd == 'n':
		nav = 'next'
	if cmd == 'prev' or cmd == 'p':
		nav = 'prev'

	jobs = find()
	for job in jobs:
		sched = job.schedule(date_from=datetime.now())
		for i in xrange(int(value)):
			print getattr(sched, 'get_'+nav)().strftime('%Y-%m-%d %I:%M %p')

def freq(arg=args.freq):
	cmd = arg

	nav = ''
	if cmd == 'd' or cmd == 'day':
		nav = 'day'
	if cmd == 'y' or cmd == 'year':
		nav = 'year'

	jobs = find()
	for job in jobs:
		if nav == 'day': print job.frequency_per_day()
		if nav == 'year': print job.frequency_per_day() * job.frequency_per_year()

ACTIONS = ['add', 'edit', 'remove', 'log', 'list', 'schedule', 'freq']

local_vars = locals()
actions = dict((k, v) for k, v in local_vars.items() if k in ACTIONS)

for action, action_func in actions.items():
	if getattr(args, action):
		action_func()