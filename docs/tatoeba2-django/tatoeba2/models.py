# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Remove `managed = False` lines if you wish to allow Django to create and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
#
# Also note: You'll have to insert the output of 'django-admin.py sqlcustom [appname]'
# into your database.
from __future__ import unicode_literals

from django.db import models

class Acos(models.Model):
    id = models.AutoField(primary_key=True)
    parent_id = models.IntegerField(blank=True, null=True)
    model = models.CharField(max_length=255, blank=True)
    foreign_key = models.IntegerField(blank=True, null=True)
    alias = models.CharField(max_length=255, blank=True)
    lft = models.IntegerField(blank=True, null=True)
    rght = models.IntegerField(blank=True, null=True)
    class Meta:
        db_table = 'acos'

class Aros(models.Model):
    id = models.AutoField(primary_key=True)
    parent_id = models.IntegerField(blank=True, null=True)
    model = models.CharField(max_length=255, blank=True)
    foreign_key = models.IntegerField(blank=True, null=True)
    alias = models.CharField(max_length=255, blank=True)
    lft = models.IntegerField(blank=True, null=True)
    rght = models.IntegerField(blank=True, null=True)
    class Meta:
        db_table = 'aros'

class ArosAcos(models.Model):
    id = models.AutoField(primary_key=True)
    aro_id = models.IntegerField()
    aco_id = models.IntegerField()
    field_create = models.CharField(db_column='_create', max_length=2) # Field renamed because it started with '_'.
    field_read = models.CharField(db_column='_read', max_length=2) # Field renamed because it started with '_'.
    field_update = models.CharField(db_column='_update', max_length=2) # Field renamed because it started with '_'.
    field_delete = models.CharField(db_column='_delete', max_length=2) # Field renamed because it started with '_'.
    class Meta:
        db_table = 'aros_acos'

# Datetime conversion back and forth from MySQL to Python fails when datetime
# is zero. MySQL allows datetime to be zero, while Python doesn't. When Python
# is unable to interpret a datetime, it replaces it by None (see MySQLdb.times,
# def DateTime_or_None), which stands for NULL to MySQL, whereas the column
# can't be NULL, so MySQL complains.
class ZeroedDateTimeField(models.DateTimeField):
    def get_db_prep_value(self, value, connection, prepared=False):
        value = super(ZeroedDateTimeField, self).get_db_prep_value(value, connection, prepared)
        if value is None:
            return '0000-00-00 00:00:00'
        else:
            return value

class Contributions(models.Model):
    sentence_id = models.IntegerField()
    sentence_lang = models.CharField(max_length=4, blank=True)
    translation_id = models.IntegerField(blank=True, null=True)
    translation_lang = models.CharField(max_length=4, blank=True)
    script = models.CharField(max_length=4, blank=True)
    text = models.CharField(max_length=1500)
    action = models.CharField(max_length=6)
    user_id = models.IntegerField(blank=True, null=True)
    datetime = ZeroedDateTimeField()
    ip = models.CharField(max_length=15, blank=True)
    type = models.CharField(max_length=8)
    id = models.AutoField(primary_key=True, unique=True)
    class Meta:
        db_table = 'contributions'

class ContributionsStats(models.Model):
    id = models.AutoField(primary_key=True, unique=True)
    date = models.DateField(blank=True, null=True)
    lang = models.CharField(max_length=4, blank=True)
    sentences = models.IntegerField(blank=True, null=True)
    action = models.CharField(max_length=6)
    type = models.CharField(max_length=8)

    class Meta:
        db_table = 'contributions_stats'

class FavoritesUsers(models.Model):
    id = models.AutoField(primary_key=True)
    favorite_id = models.IntegerField()
    user_id = models.IntegerField()
    created = models.DateTimeField(blank=True, null=True)
    class Meta:
        db_table = 'favorites_users'
        unique_together = ('favorite_id', 'user_id')

class FollowersUsers(models.Model):
    follower_id = models.IntegerField()
    user_id = models.IntegerField()
    class Meta:
        db_table = 'followers_users'

class Groups(models.Model):
    id = models.AutoField(primary_key=True)
    name = models.CharField(max_length=100)
    created = models.DateTimeField(blank=True, null=True)
    modified = models.DateTimeField(blank=True, null=True)
    class Meta:
        db_table = 'groups'

class Languages(models.Model):
    id = models.AutoField(primary_key=True)
    code = models.CharField(unique=True, max_length=4, blank=True)
    sentences = models.IntegerField()
    audio = models.IntegerField()
    group_1 = models.IntegerField()
    group_2 = models.IntegerField()
    group_3 = models.IntegerField()
    group_4 = models.IntegerField()
    level_0 = models.IntegerField()
    level_1 = models.IntegerField()
    level_2 = models.IntegerField()
    level_3 = models.IntegerField()
    level_4 = models.IntegerField()
    level_5 = models.IntegerField()
    level_unknown = models.IntegerField()

    class Meta:
        db_table = 'languages'

class LastContributions(models.Model):
    sentence_id = models.IntegerField()
    sentence_lang = models.CharField(max_length=4, blank=True)
    translation_id = models.IntegerField(blank=True, null=True)
    translation_lang = models.CharField(max_length=4, blank=True)
    script = models.CharField(max_length=4, blank=True)
    text = models.CharField(max_length=1500)
    action = models.CharField(max_length=6)
    user_id = models.IntegerField(blank=True, null=True)
    datetime = models.DateTimeField()
    ip = models.CharField(max_length=15, blank=True)
    type = models.CharField(max_length=8)
    id = models.AutoField(primary_key=True)
    class Meta:
        db_table = 'last_contributions'

class PrivateMessages(models.Model):
    id = models.AutoField(primary_key=True)
    recpt = models.IntegerField()
    sender = models.IntegerField()
    user_id = models.IntegerField()
    date = models.DateTimeField()
    folder = models.CharField(max_length=5)
    title = models.CharField(max_length=255)
    content = models.TextField()
    isnonread = models.IntegerField()
    class Meta:
        db_table = 'private_messages'

class ReindexFlags(models.Model):
    id = models.AutoField(primary_key=True)
    sentence_id = models.IntegerField()
    lang_id = models.IntegerField(blank=True, null=True)
    indexed = models.IntegerField()

    class Meta:
        db_table = 'reindex_flags'

class SentenceAnnotations(models.Model):
    id = models.AutoField(primary_key=True)
    sentence_id = models.IntegerField()
    meaning_id = models.IntegerField()
    text = models.CharField(max_length=2000)
    modified = models.DateTimeField()
    user_id = models.IntegerField()
    class Meta:
        db_table = 'sentence_annotations'

class SentenceAnnotationsOld(models.Model):
    id = models.AutoField(primary_key=True)
    sentence_id = models.IntegerField()
    meaning_id = models.IntegerField()
    dico_id = models.IntegerField()
    text = models.CharField(max_length=2000)
    class Meta:
        db_table = 'sentence_annotations_old'

class SentenceComments(models.Model):
    id = models.AutoField(primary_key=True)
    sentence_id = models.IntegerField()
    lang = models.CharField(max_length=4, blank=True)
    text = models.TextField()
    user_id = models.IntegerField()
    created = models.DateTimeField()
    modified = models.DateTimeField(blank=True, null=True)
    hidden = models.IntegerField()
    class Meta:
        db_table = 'sentence_comments'

class Sentences(models.Model):
    id = models.AutoField(primary_key=True)
    lang = models.CharField(max_length=4, blank=True)
    text = models.CharField(max_length=1500)
    correctness = models.IntegerField(blank=True, null=True)
    user_id = models.IntegerField(blank=True, null=True)
    created = models.DateTimeField(blank=True, null=True)
    modified = models.DateTimeField(blank=True, null=True)
    dico_id = models.IntegerField(blank=True, null=True)
    hasaudio = models.CharField(max_length=10)
    lang_id = models.IntegerField(blank=True, null=True)
    script = models.CharField(max_length=4, blank=True)
    class Meta:
        db_table = 'sentences'

class SentencesLists(models.Model):
    id = models.AutoField(primary_key=True)
    is_public = models.IntegerField()
    name = models.CharField(max_length=450)
    user_id = models.IntegerField(blank=True, null=True)
    numberofsentences = models.IntegerField(db_column='numberOfSentences') # Field name made lowercase.
    created = models.DateTimeField(blank=True, null=True)
    modified = models.DateTimeField(blank=True, null=True)
    visibility = models.CharField(max_length=8)
    editable_by = models.CharField(max_length=7)

    class Meta:
        db_table = 'sentences_lists'

class SentencesSentencesLists(models.Model):
    id = models.AutoField(primary_key=True)
    sentences_list_id = models.IntegerField()
    sentence_id = models.IntegerField()
    created = models.DateTimeField(blank=True, null=True)
    class Meta:
        db_table = 'sentences_sentences_lists'
        unique_together = ('sentences_list_id', 'sentence_id')

class SentencesTranslations(models.Model):
    id = models.AutoField(primary_key=True)
    sentence_id = models.IntegerField()
    translation_id = models.IntegerField()
    sentence_lang = models.CharField(max_length=4, blank=True)
    translation_lang = models.CharField(max_length=4, blank=True)
    distance = models.IntegerField()
    class Meta:
        db_table = 'sentences_translations'
        unique_together = ('sentence_id', 'translation_id')

class SinogramSubglyphs(models.Model):
    sinogram_id = models.IntegerField()
    glyph = models.CharField(max_length=2, blank=True)
    subglyph = models.CharField(max_length=2)
    class Meta:
        db_table = 'sinogram_subglyphs'

class Sinograms(models.Model):
    id = models.AutoField(primary_key=True)
    utf = models.CharField(max_length=8)
    glyph = models.CharField(max_length=10)
    strokes = models.IntegerField(blank=True, null=True)
    english = models.TextField(blank=True)
    chin_trad = models.CharField(db_column='chin-trad', max_length=10, blank=True) # Field renamed to remove unsuitable characters.
    chin_simpl = models.CharField(db_column='chin-simpl', max_length=10, blank=True) # Field renamed to remove unsuitable characters.
    chin_pinyin = models.CharField(db_column='chin-pinyin', max_length=255, blank=True) # Field renamed to remove unsuitable characters.
    jap_on = models.CharField(db_column='jap-on', max_length=255, blank=True) # Field renamed to remove unsuitable characters.
    jap_kun = models.CharField(db_column='jap-kun', max_length=255, blank=True) # Field renamed to remove unsuitable characters.
    frequency = models.FloatField()
    checked = models.IntegerField()
    subcharacterslist = models.CharField(max_length=255, blank=True)
    usedbylist = models.CharField(db_column='usedByList', max_length=255, blank=True) # Field name made lowercase.
    class Meta:
        db_table = 'sinograms'

class Tags(models.Model):
    id = models.AutoField(primary_key=True)
    internal_name = models.CharField(max_length=50)
    name = models.CharField(max_length=50)
    description = models.CharField(max_length=500, blank=True)
    user_id = models.IntegerField(blank=True, null=True)
    created = models.DateTimeField(blank=True, null=True)
    nbrofsentences = models.IntegerField(db_column='nbrOfSentences') # Field name made lowercase.
    class Meta:
        db_table = 'tags'

class TagsSentences(models.Model):
    id = models.AutoField(primary_key=True)
    tag_id = models.IntegerField()
    user_id = models.IntegerField(blank=True, null=True)
    sentence_id = models.IntegerField(blank=True, null=True)
    added_time = models.DateTimeField(blank=True, null=True)
    class Meta:
        db_table = 'tags_sentences'

class Transcriptions(models.Model):
    id = models.AutoField(primary_key=True)
    sentence_id = models.IntegerField()
    script = models.CharField(max_length=4)
    text = models.CharField(max_length=10000)
    user_id = models.IntegerField(blank=True, null=True)
    created = models.DateTimeField()
    modified = models.DateTimeField()

    class Meta:
        db_table = 'transcriptions'
        unique_together = ('sentence_id', 'script')

class Users(models.Model):
    id = models.AutoField(primary_key=True)
    username = models.CharField(unique=True, max_length=20)
    password = models.CharField(max_length=50)
    email = models.CharField(unique=True, max_length=100)
    since = models.DateTimeField()
    last_time_active = models.IntegerField()
    level = models.IntegerField()
    group_id = models.IntegerField()
    send_notifications = models.IntegerField()
    name = models.CharField(max_length=255)
    birthday = models.DateTimeField(blank=True, null=True)
    description = models.TextField()
    settings = models.TextField()
    homepage = models.CharField(max_length=255)
    image = models.CharField(max_length=255)
    country_id = models.CharField(max_length=2, blank=True)
    class Meta:
        db_table = 'users'

class UsersLanguages(models.Model):
    id = models.AutoField(primary_key=True)
    of_user_id = models.IntegerField()
    by_user_id = models.IntegerField()
    language_code = models.CharField(max_length=4)
    level = models.IntegerField(blank=True, null=True)
    level_approval_status = models.CharField(max_length=10)
    details = models.TextField()
    created = models.DateTimeField(blank=True, null=True)
    modified = models.DateTimeField(blank=True, null=True)
    class Meta:
        db_table = 'users_languages'
        unique_together = ('of_user_id', 'by_user_id', 'language_code')

class Visitors(models.Model):
    ip = models.CharField(primary_key=True, unique=True, max_length=15)
    timestamp = models.IntegerField()
    class Meta:
        db_table = 'visitors'

class Wall(models.Model):
    id = models.AutoField(primary_key=True)
    owner = models.IntegerField()
    parent_id = models.IntegerField(blank=True, null=True)
    date = models.DateTimeField()
    title = models.CharField(max_length=255)
    content = models.TextField()
    lft = models.IntegerField(blank=True, null=True)
    rght = models.IntegerField(blank=True, null=True)
    hidden = models.IntegerField()
    modified = models.DateTimeField(blank=True, null=True)
    class Meta:
        db_table = 'wall'

class WallThreadsLastMessage(models.Model):
    id = models.IntegerField(primary_key=True)
    last_message_date = models.DateTimeField()
    class Meta:
        db_table = 'wall_threads_last_message'

class UsersSentences(models.Model):
    id = models.AutoField(primary_key=True)
    user_id = models.IntegerField()
    sentence_id = models.IntegerField()
    correctness = models.IntegerField()
    created = models.DateTimeField(blank=True, null=True)
    modified = models.DateTimeField(blank=True, null=True)
    dirty = models.IntegerField(blank=True, null=True)

    class Meta:
        db_table = 'users_sentences'
        unique_together = ('user_id', 'sentence_id')

from django.conf import settings

if not settings.MANAGE_DB:
    import sys
    import inspect

    mmbrs = inspect.getmembers(sys.modules[__name__], inspect.isclass)
    tbls = [m[1] for m in mmbrs]
    for tbl in tbls:
        if hasattr(tbl, '_meta'): tbl._meta.managed = False
