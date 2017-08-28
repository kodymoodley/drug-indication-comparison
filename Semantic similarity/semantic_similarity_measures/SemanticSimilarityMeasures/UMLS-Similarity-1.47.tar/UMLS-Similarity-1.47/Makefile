# This Makefile is for the UMLS::Similarity extension to perl.
#
# It was generated automatically by MakeMaker version
# 7.1002 (Revision: 71002) from the contents of
# Makefile.PL. Don't edit this file, edit Makefile.PL instead.
#
#       ANY CHANGES MADE HERE WILL BE LOST!
#
#   MakeMaker ARGV: ()
#

#   MakeMaker Parameters:

#     ABSTRACT_FROM => q[lib/UMLS/Similarity.pm]
#     AUTHOR => [q[Bridget McInnes <bthomson@cs.umn.edu>, Ted Pedersen <tpederse@d.umn.edu>, Serguei Pakhomov <pakh0002 at umn.edu>, Ying Liu <liux0395@umn.edu>]]
#     BUILD_REQUIRES => {  }
#     CONFIGURE_REQUIRES => {  }
#     EXE_FILES => [q[utils/umls-similarity.pl], q[utils/umls_similarity_server.pl], q[utils/query-umls-similarity-webinterface.pl], q[utils/vector-input.pl], q[utils/spearmans.pl], q[utils/create-icfrequency.pl], q[utils/create-icpropagation.pl], q[utils/sim2r.pl], q[utils/SignificanceTesting.r]]
#     NAME => q[UMLS::Similarity]
#     PREREQ_PM => { File::Path=>q[2.08], File::Spec=>q[3.31], Lingua::Stem=>q[0.84], Text::NSP=>q[1.17], Text::OverlapFinder=>q[0.02], UMLS::Interface=>q[1.43] }
#     TEST_REQUIRES => {  }
#     VERSION_FROM => q[lib/UMLS/Similarity.pm]
#     dist => { COMPRESS=>q[gzip -9f], SUFFIX=>q[gz] }

# --- MakeMaker post_initialize section:


# --- MakeMaker const_config section:

# These definitions are from config.sh (via /usr/lib/perl5/core_perl/Config.pm).
# They may have been overridden via Makefile.PL or on the command line.
AR = ar
CC = gcc
CCCDLFLAGS =  
CCDLFLAGS =  
DLEXT = dll
DLSRC = dl_dlopen.xs
EXE_EXT = .exe
FULL_AR = /usr/bin/ar
LD = g++
LDDLFLAGS =  --shared  -Wl,--enable-auto-import -Wl,--export-all-symbols -Wl,--enable-auto-image-base -fstack-protector-strong
LDFLAGS =  -Wl,--enable-auto-import -Wl,--export-all-symbols -Wl,--enable-auto-image-base -fstack-protector-strong
LIBC = /usr/lib/libmsys-2.0.a
LIB_EXT = .a
OBJ_EXT = .o
OSNAME = msys
OSVERS = 2.7.0\(0.30653\)
RANLIB = :
SITELIBEXP = /usr/share/perl5/site_perl
SITEARCHEXP = /usr/lib/perl5/site_perl
SO = dll
VENDORARCHEXP = /usr/lib/perl5/vendor_perl
VENDORLIBEXP = /usr/share/perl5/vendor_perl


# --- MakeMaker constants section:
AR_STATIC_ARGS = cr
DIRFILESEP = /
DFSEP = $(DIRFILESEP)
NAME = UMLS::Similarity
NAME_SYM = UMLS_Similarity
VERSION = 1.47
VERSION_MACRO = VERSION
VERSION_SYM = 1_47
DEFINE_VERSION = -D$(VERSION_MACRO)=\"$(VERSION)\"
XS_VERSION = 1.47
XS_VERSION_MACRO = XS_VERSION
XS_DEFINE_VERSION = -D$(XS_VERSION_MACRO)=\"$(XS_VERSION)\"
INST_ARCHLIB = blib/arch
INST_SCRIPT = blib/script
INST_BIN = blib/bin
INST_LIB = blib/lib
INST_MAN1DIR = blib/man1
INST_MAN3DIR = blib/man3
MAN1EXT = 1p
MAN3EXT = 3pm
INSTALLDIRS = site
DESTDIR = 
PREFIX = $(SITEPREFIX)
PERLPREFIX = /usr
SITEPREFIX = /usr
VENDORPREFIX = /usr
INSTALLPRIVLIB = /usr/share/perl5/core_perl
DESTINSTALLPRIVLIB = $(DESTDIR)$(INSTALLPRIVLIB)
INSTALLSITELIB = /usr/share/perl5/site_perl
DESTINSTALLSITELIB = $(DESTDIR)$(INSTALLSITELIB)
INSTALLVENDORLIB = /usr/share/perl5/vendor_perl
DESTINSTALLVENDORLIB = $(DESTDIR)$(INSTALLVENDORLIB)
INSTALLARCHLIB = /usr/lib/perl5/core_perl
DESTINSTALLARCHLIB = $(DESTDIR)$(INSTALLARCHLIB)
INSTALLSITEARCH = /usr/lib/perl5/site_perl
DESTINSTALLSITEARCH = $(DESTDIR)$(INSTALLSITEARCH)
INSTALLVENDORARCH = /usr/lib/perl5/vendor_perl
DESTINSTALLVENDORARCH = $(DESTDIR)$(INSTALLVENDORARCH)
INSTALLBIN = /usr/bin
DESTINSTALLBIN = $(DESTDIR)$(INSTALLBIN)
INSTALLSITEBIN = /usr/bin
DESTINSTALLSITEBIN = $(DESTDIR)$(INSTALLSITEBIN)
INSTALLVENDORBIN = /usr/bin
DESTINSTALLVENDORBIN = $(DESTDIR)$(INSTALLVENDORBIN)
INSTALLSCRIPT = /usr/bin/core_perl
DESTINSTALLSCRIPT = $(DESTDIR)$(INSTALLSCRIPT)
INSTALLSITESCRIPT = /usr/bin/site_perl
DESTINSTALLSITESCRIPT = $(DESTDIR)$(INSTALLSITESCRIPT)
INSTALLVENDORSCRIPT = /usr/bin/vendor_perl
DESTINSTALLVENDORSCRIPT = $(DESTDIR)$(INSTALLVENDORSCRIPT)
INSTALLMAN1DIR = /usr/share/man/man1
DESTINSTALLMAN1DIR = $(DESTDIR)$(INSTALLMAN1DIR)
INSTALLSITEMAN1DIR = /usr/share/man/man1
DESTINSTALLSITEMAN1DIR = $(DESTDIR)$(INSTALLSITEMAN1DIR)
INSTALLVENDORMAN1DIR = /usr/share/man/man1
DESTINSTALLVENDORMAN1DIR = $(DESTDIR)$(INSTALLVENDORMAN1DIR)
INSTALLMAN3DIR = /usr/share/man/man3
DESTINSTALLMAN3DIR = $(DESTDIR)$(INSTALLMAN3DIR)
INSTALLSITEMAN3DIR = /usr/share/man/man3
DESTINSTALLSITEMAN3DIR = $(DESTDIR)$(INSTALLSITEMAN3DIR)
INSTALLVENDORMAN3DIR = /usr/share/man/man3
DESTINSTALLVENDORMAN3DIR = $(DESTDIR)$(INSTALLVENDORMAN3DIR)
PERL_LIB = /usr/share/perl5/core_perl
PERL_ARCHLIB = /usr/lib/perl5/core_perl
PERL_ARCHLIBDEP = /usr/lib/perl5/core_perl
LIBPERL_A = libperl.a
FIRST_MAKEFILE = Makefile
MAKEFILE_OLD = Makefile.old
MAKE_APERL_FILE = Makefile.aperl
PERLMAINCC = $(CC)
PERL_INC = /usr/lib/perl5/core_perl/CORE
PERL_INCDEP = /usr/lib/perl5/core_perl/CORE
PERL = "/usr/bin/perl.exe"
FULLPERL = "/usr/bin/perl.exe"
ABSPERL = $(PERL)
PERLRUN = $(PERL)
FULLPERLRUN = $(FULLPERL)
ABSPERLRUN = $(ABSPERL)
PERLRUNINST = $(PERLRUN) "-I$(INST_ARCHLIB)" "-I$(INST_LIB)"
FULLPERLRUNINST = $(FULLPERLRUN) "-I$(INST_ARCHLIB)" "-I$(INST_LIB)"
ABSPERLRUNINST = $(ABSPERLRUN) "-I$(INST_ARCHLIB)" "-I$(INST_LIB)"
PERL_CORE = 0
PERM_DIR = 755
PERM_RW = 644
PERM_RWX = 755

MAKEMAKER   = /usr/share/perl5/core_perl/ExtUtils/MakeMaker.pm
MM_VERSION  = 7.1002
MM_REVISION = 71002

# FULLEXT = Pathname for extension directory (eg Foo/Bar/Oracle).
# BASEEXT = Basename part of FULLEXT. May be just equal FULLEXT. (eg Oracle)
# PARENT_NAME = NAME without BASEEXT and no trailing :: (eg Foo::Bar)
# DLBASE  = Basename part of dynamic library. May be just equal BASEEXT.
MAKE = make
FULLEXT = UMLS/Similarity
BASEEXT = Similarity
PARENT_NAME = UMLS
DLBASE = $(BASEEXT)
VERSION_FROM = lib/UMLS/Similarity.pm
OBJECT = 
LDFROM = $(OBJECT)
LINKTYPE = dynamic
BOOTDEP = 

# Handy lists of source code files:
XS_FILES = 
C_FILES  = 
O_FILES  = 
H_FILES  = 
MAN1PODS = utils/create-icfrequency.pl \
	utils/create-icpropagation.pl \
	utils/query-umls-similarity-webinterface.pl \
	utils/sim2r.pl \
	utils/spearmans.pl \
	utils/umls-similarity.pl \
	utils/umls_similarity_server.pl \
	utils/vector-input.pl
MAN3PODS = lib/UMLS/Interface.pm \
	lib/UMLS/Interface/CuiFinder.pm \
	lib/UMLS/Interface/ErrorHandler.pm \
	lib/UMLS/Interface/ICFinder.pm \
	lib/UMLS/Interface/PathFinder.pm \
	lib/UMLS/Interface/STFinder.pm \
	lib/UMLS/Similarity.pm \
	lib/UMLS/Similarity/ErrorHandler.pm \
	lib/UMLS/Similarity/batet.pm \
	lib/UMLS/Similarity/cdist.pm \
	lib/UMLS/Similarity/cmatch.pm \
	lib/UMLS/Similarity/faith.pm \
	lib/UMLS/Similarity/jcn.pm \
	lib/UMLS/Similarity/lch.pm \
	lib/UMLS/Similarity/lesk.pm \
	lib/UMLS/Similarity/lin.pm \
	lib/UMLS/Similarity/nam.pm \
	lib/UMLS/Similarity/path.pm \
	lib/UMLS/Similarity/pks.pm \
	lib/UMLS/Similarity/random.pm \
	lib/UMLS/Similarity/res.pm \
	lib/UMLS/Similarity/sanchez.pm \
	lib/UMLS/Similarity/upath.pm \
	lib/UMLS/Similarity/vector.pm \
	lib/UMLS/Similarity/wup.pm \
	lib/UMLS/Similarity/zhong.pm

# Where to build things
INST_LIBDIR      = $(INST_LIB)/UMLS
INST_ARCHLIBDIR  = $(INST_ARCHLIB)/UMLS

INST_AUTODIR     = $(INST_LIB)/auto/$(FULLEXT)
INST_ARCHAUTODIR = $(INST_ARCHLIB)/auto/$(FULLEXT)

INST_STATIC      = 
INST_DYNAMIC     = 
INST_BOOT        = 

# Extra linker info
EXPORT_LIST        = 
PERL_ARCHIVE       = $(PERL_INC)/msys-perl5_24.dll
PERL_ARCHIVEDEP    = 
PERL_ARCHIVE_AFTER = 


TO_INST_PM = lib/UMLS/Interface.pm \
	lib/UMLS/Interface/CuiFinder.pm \
	lib/UMLS/Interface/ErrorHandler.pm \
	lib/UMLS/Interface/ICFinder.pm \
	lib/UMLS/Interface/PathFinder.pm \
	lib/UMLS/Interface/STFinder.pm \
	lib/UMLS/Similarity.pm \
	lib/UMLS/Similarity/ErrorHandler.pm \
	lib/UMLS/Similarity/batet.pm \
	lib/UMLS/Similarity/cdist.pm \
	lib/UMLS/Similarity/cmatch.pm \
	lib/UMLS/Similarity/faith.pm \
	lib/UMLS/Similarity/jcn.pm \
	lib/UMLS/Similarity/lch.pm \
	lib/UMLS/Similarity/lesk.pm \
	lib/UMLS/Similarity/lin.pm \
	lib/UMLS/Similarity/nam.pm \
	lib/UMLS/Similarity/path.pm \
	lib/UMLS/Similarity/pks.pm \
	lib/UMLS/Similarity/random.pm \
	lib/UMLS/Similarity/res.pm \
	lib/UMLS/Similarity/sanchez.pm \
	lib/UMLS/Similarity/upath.pm \
	lib/UMLS/Similarity/vector.pm \
	lib/UMLS/Similarity/wup.pm \
	lib/UMLS/Similarity/zhong.pm \
	lib/UMLS/icfrequency.default.dat \
	lib/UMLS/vector-index.dat \
	lib/UMLS/vector-matrix.dat

PM_TO_BLIB = lib/UMLS/Interface.pm \
	blib/lib/UMLS/Interface.pm \
	lib/UMLS/Interface/CuiFinder.pm \
	blib/lib/UMLS/Interface/CuiFinder.pm \
	lib/UMLS/Interface/ErrorHandler.pm \
	blib/lib/UMLS/Interface/ErrorHandler.pm \
	lib/UMLS/Interface/ICFinder.pm \
	blib/lib/UMLS/Interface/ICFinder.pm \
	lib/UMLS/Interface/PathFinder.pm \
	blib/lib/UMLS/Interface/PathFinder.pm \
	lib/UMLS/Interface/STFinder.pm \
	blib/lib/UMLS/Interface/STFinder.pm \
	lib/UMLS/Similarity.pm \
	blib/lib/UMLS/Similarity.pm \
	lib/UMLS/Similarity/ErrorHandler.pm \
	blib/lib/UMLS/Similarity/ErrorHandler.pm \
	lib/UMLS/Similarity/batet.pm \
	blib/lib/UMLS/Similarity/batet.pm \
	lib/UMLS/Similarity/cdist.pm \
	blib/lib/UMLS/Similarity/cdist.pm \
	lib/UMLS/Similarity/cmatch.pm \
	blib/lib/UMLS/Similarity/cmatch.pm \
	lib/UMLS/Similarity/faith.pm \
	blib/lib/UMLS/Similarity/faith.pm \
	lib/UMLS/Similarity/jcn.pm \
	blib/lib/UMLS/Similarity/jcn.pm \
	lib/UMLS/Similarity/lch.pm \
	blib/lib/UMLS/Similarity/lch.pm \
	lib/UMLS/Similarity/lesk.pm \
	blib/lib/UMLS/Similarity/lesk.pm \
	lib/UMLS/Similarity/lin.pm \
	blib/lib/UMLS/Similarity/lin.pm \
	lib/UMLS/Similarity/nam.pm \
	blib/lib/UMLS/Similarity/nam.pm \
	lib/UMLS/Similarity/path.pm \
	blib/lib/UMLS/Similarity/path.pm \
	lib/UMLS/Similarity/pks.pm \
	blib/lib/UMLS/Similarity/pks.pm \
	lib/UMLS/Similarity/random.pm \
	blib/lib/UMLS/Similarity/random.pm \
	lib/UMLS/Similarity/res.pm \
	blib/lib/UMLS/Similarity/res.pm \
	lib/UMLS/Similarity/sanchez.pm \
	blib/lib/UMLS/Similarity/sanchez.pm \
	lib/UMLS/Similarity/upath.pm \
	blib/lib/UMLS/Similarity/upath.pm \
	lib/UMLS/Similarity/vector.pm \
	blib/lib/UMLS/Similarity/vector.pm \
	lib/UMLS/Similarity/wup.pm \
	blib/lib/UMLS/Similarity/wup.pm \
	lib/UMLS/Similarity/zhong.pm \
	blib/lib/UMLS/Similarity/zhong.pm \
	lib/UMLS/icfrequency.default.dat \
	blib/lib/UMLS/icfrequency.default.dat \
	lib/UMLS/vector-index.dat \
	blib/lib/UMLS/vector-index.dat \
	lib/UMLS/vector-matrix.dat \
	blib/lib/UMLS/vector-matrix.dat


# --- MakeMaker platform_constants section:
MM_Unix_VERSION = 7.1002
PERL_MALLOC_DEF = -DPERL_EXTMALLOC_DEF -Dmalloc=Perl_malloc -Dfree=Perl_mfree -Drealloc=Perl_realloc -Dcalloc=Perl_calloc


# --- MakeMaker tool_autosplit section:
# Usage: $(AUTOSPLITFILE) FileToSplit AutoDirToSplitInto
AUTOSPLITFILE = $(ABSPERLRUN)  -e 'use AutoSplit;  autosplit($$$$ARGV[0], $$$$ARGV[1], 0, 1, 1)' --



# --- MakeMaker tool_xsubpp section:


# --- MakeMaker tools_other section:
SHELL = /bin/sh
CHMOD = chmod
CP = cp
MV = mv
NOOP = $(TRUE)
NOECHO = @
RM_F = rm -f
RM_RF = rm -rf
TEST_F = test -f
TOUCH = touch
UMASK_NULL = umask 0
DEV_NULL = > /dev/null 2>&1
MKPATH = $(ABSPERLRUN) -MExtUtils::Command -e 'mkpath' --
EQUALIZE_TIMESTAMP = $(ABSPERLRUN) -MExtUtils::Command -e 'eqtime' --
FALSE = false
TRUE = true
ECHO = echo
ECHO_N = echo -n
UNINST = 0
VERBINST = 0
MOD_INSTALL = $(ABSPERLRUN) -MExtUtils::Install -e 'install([ from_to => {@ARGV}, verbose => '\''$(VERBINST)'\'', uninstall_shadows => '\''$(UNINST)'\'', dir_mode => '\''$(PERM_DIR)'\'' ]);' --
DOC_INSTALL = $(ABSPERLRUN) -MExtUtils::Command::MM -e 'perllocal_install' --
UNINSTALL = $(ABSPERLRUN) -MExtUtils::Command::MM -e 'uninstall' --
WARN_IF_OLD_PACKLIST = $(ABSPERLRUN) -MExtUtils::Command::MM -e 'warn_if_old_packlist' --
MACROSTART = 
MACROEND = 
USEMAKEFILE = -f
FIXIN = $(ABSPERLRUN) -MExtUtils::MY -e 'MY->fixin(shift)' --
CP_NONEMPTY = $(ABSPERLRUN) -MExtUtils::Command::MM -e 'cp_nonempty' --


# --- MakeMaker makemakerdflt section:
makemakerdflt : all
	$(NOECHO) $(NOOP)


# --- MakeMaker dist section:
TAR = tar
TARFLAGS = cvf
ZIP = zip
ZIPFLAGS = -r
COMPRESS = gzip -9f
SUFFIX = .gz
SHAR = shar
PREOP = $(NOECHO) $(NOOP)
POSTOP = $(NOECHO) $(NOOP)
TO_UNIX = $(NOECHO) $(NOOP)
CI = ci -u
RCS_LABEL = rcs -Nv$(VERSION_SYM): -q
DIST_CP = best
DIST_DEFAULT = tardist
DISTNAME = UMLS-Similarity
DISTVNAME = UMLS-Similarity-1.47


# --- MakeMaker macro section:


# --- MakeMaker depend section:


# --- MakeMaker cflags section:


# --- MakeMaker const_loadlibs section:


# --- MakeMaker const_cccmd section:


# --- MakeMaker post_constants section:


# --- MakeMaker pasthru section:

PASTHRU = LIBPERL_A="$(LIBPERL_A)"\
	LINKTYPE="$(LINKTYPE)"\
	PREFIX="$(PREFIX)"


# --- MakeMaker special_targets section:
.SUFFIXES : .xs .c .C .cpp .i .s .cxx .cc $(OBJ_EXT)

.PHONY: all config static dynamic test linkext manifest blibdirs clean realclean disttest distdir



# --- MakeMaker c_o section:


# --- MakeMaker xs_c section:


# --- MakeMaker xs_o section:


# --- MakeMaker top_targets section:
all :: pure_all manifypods
	$(NOECHO) $(NOOP)


pure_all :: config pm_to_blib subdirs linkext
	$(NOECHO) $(NOOP)

subdirs :: $(MYEXTLIB)
	$(NOECHO) $(NOOP)

config :: $(FIRST_MAKEFILE) blibdirs
	$(NOECHO) $(NOOP)

help :
	perldoc ExtUtils::MakeMaker


# --- MakeMaker blibdirs section:
blibdirs : $(INST_LIBDIR)$(DFSEP).exists $(INST_ARCHLIB)$(DFSEP).exists $(INST_AUTODIR)$(DFSEP).exists $(INST_ARCHAUTODIR)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists $(INST_SCRIPT)$(DFSEP).exists $(INST_MAN1DIR)$(DFSEP).exists $(INST_MAN3DIR)$(DFSEP).exists
	$(NOECHO) $(NOOP)

# Backwards compat with 6.18 through 6.25
blibdirs.ts : blibdirs
	$(NOECHO) $(NOOP)

$(INST_LIBDIR)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_LIBDIR)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_LIBDIR)
	$(NOECHO) $(TOUCH) $(INST_LIBDIR)$(DFSEP).exists

$(INST_ARCHLIB)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_ARCHLIB)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_ARCHLIB)
	$(NOECHO) $(TOUCH) $(INST_ARCHLIB)$(DFSEP).exists

$(INST_AUTODIR)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_AUTODIR)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_AUTODIR)
	$(NOECHO) $(TOUCH) $(INST_AUTODIR)$(DFSEP).exists

$(INST_ARCHAUTODIR)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_ARCHAUTODIR)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_ARCHAUTODIR)
	$(NOECHO) $(TOUCH) $(INST_ARCHAUTODIR)$(DFSEP).exists

$(INST_BIN)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_BIN)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_BIN)
	$(NOECHO) $(TOUCH) $(INST_BIN)$(DFSEP).exists

$(INST_SCRIPT)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_SCRIPT)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_SCRIPT)
	$(NOECHO) $(TOUCH) $(INST_SCRIPT)$(DFSEP).exists

$(INST_MAN1DIR)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_MAN1DIR)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_MAN1DIR)
	$(NOECHO) $(TOUCH) $(INST_MAN1DIR)$(DFSEP).exists

$(INST_MAN3DIR)$(DFSEP).exists :: Makefile.PL
	$(NOECHO) $(MKPATH) $(INST_MAN3DIR)
	$(NOECHO) $(CHMOD) $(PERM_DIR) $(INST_MAN3DIR)
	$(NOECHO) $(TOUCH) $(INST_MAN3DIR)$(DFSEP).exists



# --- MakeMaker linkext section:

linkext :: $(LINKTYPE)
	$(NOECHO) $(NOOP)


# --- MakeMaker dlsyms section:


# --- MakeMaker dynamic_bs section:

BOOTSTRAP =


# --- MakeMaker dynamic section:

dynamic :: $(FIRST_MAKEFILE) $(BOOTSTRAP) $(INST_DYNAMIC)
	$(NOECHO) $(NOOP)


# --- MakeMaker dynamic_lib section:


# --- MakeMaker static section:

## $(INST_PM) has been moved to the all: target.
## It remains here for awhile to allow for old usage: "make static"
static :: $(FIRST_MAKEFILE) $(INST_STATIC)
	$(NOECHO) $(NOOP)


# --- MakeMaker static_lib section:


# --- MakeMaker manifypods section:

POD2MAN_EXE = $(PERLRUN) "-MExtUtils::Command::MM" -e pod2man "--"
POD2MAN = $(POD2MAN_EXE)


manifypods : pure_all  \
	lib/UMLS/Interface.pm \
	lib/UMLS/Interface/CuiFinder.pm \
	lib/UMLS/Interface/ErrorHandler.pm \
	lib/UMLS/Interface/ICFinder.pm \
	lib/UMLS/Interface/PathFinder.pm \
	lib/UMLS/Interface/STFinder.pm \
	lib/UMLS/Similarity.pm \
	lib/UMLS/Similarity/ErrorHandler.pm \
	lib/UMLS/Similarity/batet.pm \
	lib/UMLS/Similarity/cdist.pm \
	lib/UMLS/Similarity/cmatch.pm \
	lib/UMLS/Similarity/faith.pm \
	lib/UMLS/Similarity/jcn.pm \
	lib/UMLS/Similarity/lch.pm \
	lib/UMLS/Similarity/lesk.pm \
	lib/UMLS/Similarity/lin.pm \
	lib/UMLS/Similarity/nam.pm \
	lib/UMLS/Similarity/path.pm \
	lib/UMLS/Similarity/pks.pm \
	lib/UMLS/Similarity/random.pm \
	lib/UMLS/Similarity/res.pm \
	lib/UMLS/Similarity/sanchez.pm \
	lib/UMLS/Similarity/upath.pm \
	lib/UMLS/Similarity/vector.pm \
	lib/UMLS/Similarity/wup.pm \
	lib/UMLS/Similarity/zhong.pm \
	utils/create-icfrequency.pl \
	utils/create-icpropagation.pl \
	utils/query-umls-similarity-webinterface.pl \
	utils/sim2r.pl \
	utils/spearmans.pl \
	utils/umls-similarity.pl \
	utils/umls_similarity_server.pl \
	utils/vector-input.pl
	$(NOECHO) $(POD2MAN) --section=1 --perm_rw=$(PERM_RW) -u \
	  utils/create-icfrequency.pl $(INST_MAN1DIR)/create-icfrequency.pl.$(MAN1EXT) \
	  utils/create-icpropagation.pl $(INST_MAN1DIR)/create-icpropagation.pl.$(MAN1EXT) \
	  utils/query-umls-similarity-webinterface.pl $(INST_MAN1DIR)/query-umls-similarity-webinterface.pl.$(MAN1EXT) \
	  utils/sim2r.pl $(INST_MAN1DIR)/sim2r.pl.$(MAN1EXT) \
	  utils/spearmans.pl $(INST_MAN1DIR)/spearmans.pl.$(MAN1EXT) \
	  utils/umls-similarity.pl $(INST_MAN1DIR)/umls-similarity.pl.$(MAN1EXT) \
	  utils/umls_similarity_server.pl $(INST_MAN1DIR)/umls_similarity_server.pl.$(MAN1EXT) \
	  utils/vector-input.pl $(INST_MAN1DIR)/vector-input.pl.$(MAN1EXT) 
	$(NOECHO) $(POD2MAN) --section=3 --perm_rw=$(PERM_RW) -u \
	  lib/UMLS/Interface.pm $(INST_MAN3DIR)/UMLS.Interface.$(MAN3EXT) \
	  lib/UMLS/Interface/CuiFinder.pm $(INST_MAN3DIR)/UMLS.Interface.CuiFinder.$(MAN3EXT) \
	  lib/UMLS/Interface/ErrorHandler.pm $(INST_MAN3DIR)/UMLS.Interface.ErrorHandler.$(MAN3EXT) \
	  lib/UMLS/Interface/ICFinder.pm $(INST_MAN3DIR)/UMLS.Interface.ICFinder.$(MAN3EXT) \
	  lib/UMLS/Interface/PathFinder.pm $(INST_MAN3DIR)/UMLS.Interface.PathFinder.$(MAN3EXT) \
	  lib/UMLS/Interface/STFinder.pm $(INST_MAN3DIR)/UMLS.Interface.STFinder.$(MAN3EXT) \
	  lib/UMLS/Similarity.pm $(INST_MAN3DIR)/UMLS.Similarity.$(MAN3EXT) \
	  lib/UMLS/Similarity/ErrorHandler.pm $(INST_MAN3DIR)/UMLS.Similarity.ErrorHandler.$(MAN3EXT) \
	  lib/UMLS/Similarity/batet.pm $(INST_MAN3DIR)/UMLS.Similarity.batet.$(MAN3EXT) \
	  lib/UMLS/Similarity/cdist.pm $(INST_MAN3DIR)/UMLS.Similarity.cdist.$(MAN3EXT) \
	  lib/UMLS/Similarity/cmatch.pm $(INST_MAN3DIR)/UMLS.Similarity.cmatch.$(MAN3EXT) \
	  lib/UMLS/Similarity/faith.pm $(INST_MAN3DIR)/UMLS.Similarity.faith.$(MAN3EXT) \
	  lib/UMLS/Similarity/jcn.pm $(INST_MAN3DIR)/UMLS.Similarity.jcn.$(MAN3EXT) \
	  lib/UMLS/Similarity/lch.pm $(INST_MAN3DIR)/UMLS.Similarity.lch.$(MAN3EXT) \
	  lib/UMLS/Similarity/lesk.pm $(INST_MAN3DIR)/UMLS.Similarity.lesk.$(MAN3EXT) \
	  lib/UMLS/Similarity/lin.pm $(INST_MAN3DIR)/UMLS.Similarity.lin.$(MAN3EXT) \
	  lib/UMLS/Similarity/nam.pm $(INST_MAN3DIR)/UMLS.Similarity.nam.$(MAN3EXT) \
	  lib/UMLS/Similarity/path.pm $(INST_MAN3DIR)/UMLS.Similarity.path.$(MAN3EXT) \
	  lib/UMLS/Similarity/pks.pm $(INST_MAN3DIR)/UMLS.Similarity.pks.$(MAN3EXT) \
	  lib/UMLS/Similarity/random.pm $(INST_MAN3DIR)/UMLS.Similarity.random.$(MAN3EXT) \
	  lib/UMLS/Similarity/res.pm $(INST_MAN3DIR)/UMLS.Similarity.res.$(MAN3EXT) \
	  lib/UMLS/Similarity/sanchez.pm $(INST_MAN3DIR)/UMLS.Similarity.sanchez.$(MAN3EXT) \
	  lib/UMLS/Similarity/upath.pm $(INST_MAN3DIR)/UMLS.Similarity.upath.$(MAN3EXT) \
	  lib/UMLS/Similarity/vector.pm $(INST_MAN3DIR)/UMLS.Similarity.vector.$(MAN3EXT) \
	  lib/UMLS/Similarity/wup.pm $(INST_MAN3DIR)/UMLS.Similarity.wup.$(MAN3EXT) \
	  lib/UMLS/Similarity/zhong.pm $(INST_MAN3DIR)/UMLS.Similarity.zhong.$(MAN3EXT) 




# --- MakeMaker processPL section:


# --- MakeMaker installbin section:

EXE_FILES = utils/umls-similarity.pl utils/umls_similarity_server.pl utils/query-umls-similarity-webinterface.pl utils/vector-input.pl utils/spearmans.pl utils/create-icfrequency.pl utils/create-icpropagation.pl utils/sim2r.pl utils/SignificanceTesting.r

pure_all :: $(INST_SCRIPT)/create-icpropagation.pl $(INST_SCRIPT)/SignificanceTesting.r $(INST_SCRIPT)/query-umls-similarity-webinterface.pl $(INST_SCRIPT)/create-icfrequency.pl $(INST_SCRIPT)/vector-input.pl $(INST_SCRIPT)/spearmans.pl $(INST_SCRIPT)/sim2r.pl $(INST_SCRIPT)/umls_similarity_server.pl $(INST_SCRIPT)/umls-similarity.pl
	$(NOECHO) $(NOOP)

realclean ::
	$(RM_F) \
	  $(INST_SCRIPT)/create-icpropagation.pl $(INST_SCRIPT)/SignificanceTesting.r \
	  $(INST_SCRIPT)/query-umls-similarity-webinterface.pl $(INST_SCRIPT)/create-icfrequency.pl \
	  $(INST_SCRIPT)/vector-input.pl $(INST_SCRIPT)/spearmans.pl \
	  $(INST_SCRIPT)/sim2r.pl $(INST_SCRIPT)/umls_similarity_server.pl \
	  $(INST_SCRIPT)/umls-similarity.pl 

$(INST_SCRIPT)/create-icpropagation.pl : utils/create-icpropagation.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/create-icpropagation.pl
	$(CP) utils/create-icpropagation.pl $(INST_SCRIPT)/create-icpropagation.pl
	$(FIXIN) $(INST_SCRIPT)/create-icpropagation.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/create-icpropagation.pl

$(INST_SCRIPT)/SignificanceTesting.r : utils/SignificanceTesting.r $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/SignificanceTesting.r
	$(CP) utils/SignificanceTesting.r $(INST_SCRIPT)/SignificanceTesting.r
	$(FIXIN) $(INST_SCRIPT)/SignificanceTesting.r
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/SignificanceTesting.r

$(INST_SCRIPT)/query-umls-similarity-webinterface.pl : utils/query-umls-similarity-webinterface.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/query-umls-similarity-webinterface.pl
	$(CP) utils/query-umls-similarity-webinterface.pl $(INST_SCRIPT)/query-umls-similarity-webinterface.pl
	$(FIXIN) $(INST_SCRIPT)/query-umls-similarity-webinterface.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/query-umls-similarity-webinterface.pl

$(INST_SCRIPT)/create-icfrequency.pl : utils/create-icfrequency.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/create-icfrequency.pl
	$(CP) utils/create-icfrequency.pl $(INST_SCRIPT)/create-icfrequency.pl
	$(FIXIN) $(INST_SCRIPT)/create-icfrequency.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/create-icfrequency.pl

$(INST_SCRIPT)/vector-input.pl : utils/vector-input.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/vector-input.pl
	$(CP) utils/vector-input.pl $(INST_SCRIPT)/vector-input.pl
	$(FIXIN) $(INST_SCRIPT)/vector-input.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/vector-input.pl

$(INST_SCRIPT)/spearmans.pl : utils/spearmans.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/spearmans.pl
	$(CP) utils/spearmans.pl $(INST_SCRIPT)/spearmans.pl
	$(FIXIN) $(INST_SCRIPT)/spearmans.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/spearmans.pl

$(INST_SCRIPT)/sim2r.pl : utils/sim2r.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/sim2r.pl
	$(CP) utils/sim2r.pl $(INST_SCRIPT)/sim2r.pl
	$(FIXIN) $(INST_SCRIPT)/sim2r.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/sim2r.pl

$(INST_SCRIPT)/umls_similarity_server.pl : utils/umls_similarity_server.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/umls_similarity_server.pl
	$(CP) utils/umls_similarity_server.pl $(INST_SCRIPT)/umls_similarity_server.pl
	$(FIXIN) $(INST_SCRIPT)/umls_similarity_server.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/umls_similarity_server.pl

$(INST_SCRIPT)/umls-similarity.pl : utils/umls-similarity.pl $(FIRST_MAKEFILE) $(INST_SCRIPT)$(DFSEP).exists $(INST_BIN)$(DFSEP).exists
	$(NOECHO) $(RM_F) $(INST_SCRIPT)/umls-similarity.pl
	$(CP) utils/umls-similarity.pl $(INST_SCRIPT)/umls-similarity.pl
	$(FIXIN) $(INST_SCRIPT)/umls-similarity.pl
	-$(NOECHO) $(CHMOD) $(PERM_RWX) $(INST_SCRIPT)/umls-similarity.pl



# --- MakeMaker subdirs section:

# none

# --- MakeMaker clean_subdirs section:
clean_subdirs :
	$(NOECHO) $(NOOP)


# --- MakeMaker clean section:

# Delete temporary files but do not touch installed files. We don't delete
# the Makefile here so a later make realclean still has a makefile to use.

clean :: clean_subdirs
	- $(RM_F) \
	  $(BASEEXT).bso $(BASEEXT).def \
	  $(BASEEXT).exp $(BASEEXT).x \
	  $(BOOTSTRAP) $(INST_ARCHAUTODIR)/extralibs.all \
	  $(INST_ARCHAUTODIR)/extralibs.ld $(MAKE_APERL_FILE) \
	  *$(LIB_EXT) *$(OBJ_EXT) \
	  *perl.core MYMETA.json \
	  MYMETA.yml blibdirs.ts \
	  core core.*perl.*.? \
	  core.[0-9] core.[0-9][0-9] \
	  core.[0-9][0-9][0-9] core.[0-9][0-9][0-9][0-9] \
	  core.[0-9][0-9][0-9][0-9][0-9] lib$(BASEEXT).def \
	  mon.out perl \
	  perl$(EXE_EXT) perl.exe \
	  perlmain.c pm_to_blib \
	  pm_to_blib.ts so_locations \
	  tmon.out 
	- $(RM_RF) \
	  blib 
	  $(NOECHO) $(RM_F) $(MAKEFILE_OLD)
	- $(MV) $(FIRST_MAKEFILE) $(MAKEFILE_OLD) $(DEV_NULL)


# --- MakeMaker realclean_subdirs section:
realclean_subdirs :
	$(NOECHO) $(NOOP)


# --- MakeMaker realclean section:
# Delete temporary files (via clean) and also delete dist files
realclean purge ::  clean realclean_subdirs
	- $(RM_F) \
	  $(FIRST_MAKEFILE) $(MAKEFILE_OLD) 
	- $(RM_RF) \
	  $(DISTVNAME) 


# --- MakeMaker metafile section:
metafile : create_distdir
	$(NOECHO) $(ECHO) Generating META.yml
	$(NOECHO) $(ECHO) '---' > META_new.yml
	$(NOECHO) $(ECHO) 'abstract: '\''A suite of Perl modules that implement a number  of semantic similarity measures in order to calculate the similarity  between two concepts in the UMLS. The measures use the UMLS-Interface  module to access the UMLS present in a mysql database.'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'author:' >> META_new.yml
	$(NOECHO) $(ECHO) '  - '\''Bridget McInnes <bthomson@cs.umn.edu>, Ted Pedersen <tpederse@d.umn.edu>, Serguei Pakhomov <pakh0002 at umn.edu>, Ying Liu <liux0395@umn.edu>'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'build_requires:' >> META_new.yml
	$(NOECHO) $(ECHO) '  ExtUtils::MakeMaker: '\''0'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'configure_requires:' >> META_new.yml
	$(NOECHO) $(ECHO) '  ExtUtils::MakeMaker: '\''0'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'dynamic_config: 1' >> META_new.yml
	$(NOECHO) $(ECHO) 'generated_by: '\''ExtUtils::MakeMaker version 7.1002, CPAN::Meta::Converter version 2.150005'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'license: unknown' >> META_new.yml
	$(NOECHO) $(ECHO) 'meta-spec:' >> META_new.yml
	$(NOECHO) $(ECHO) '  url: http://module-build.sourceforge.net/META-spec-v1.4.html' >> META_new.yml
	$(NOECHO) $(ECHO) '  version: '\''1.4'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'name: UMLS-Similarity' >> META_new.yml
	$(NOECHO) $(ECHO) 'no_index:' >> META_new.yml
	$(NOECHO) $(ECHO) '  directory:' >> META_new.yml
	$(NOECHO) $(ECHO) '    - t' >> META_new.yml
	$(NOECHO) $(ECHO) '    - inc' >> META_new.yml
	$(NOECHO) $(ECHO) 'requires:' >> META_new.yml
	$(NOECHO) $(ECHO) '  File::Path: '\''2.08'\''' >> META_new.yml
	$(NOECHO) $(ECHO) '  File::Spec: '\''3.31'\''' >> META_new.yml
	$(NOECHO) $(ECHO) '  Lingua::Stem: '\''0.84'\''' >> META_new.yml
	$(NOECHO) $(ECHO) '  Text::NSP: '\''1.17'\''' >> META_new.yml
	$(NOECHO) $(ECHO) '  Text::OverlapFinder: '\''0.02'\''' >> META_new.yml
	$(NOECHO) $(ECHO) '  UMLS::Interface: '\''1.43'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'version: '\''1.47'\''' >> META_new.yml
	$(NOECHO) $(ECHO) 'x_serialization_backend: '\''CPAN::Meta::YAML version 0.018'\''' >> META_new.yml
	-$(NOECHO) $(MV) META_new.yml $(DISTVNAME)/META.yml
	$(NOECHO) $(ECHO) Generating META.json
	$(NOECHO) $(ECHO) '{' > META_new.json
	$(NOECHO) $(ECHO) '   "abstract" : "A suite of Perl modules that implement a number  of semantic similarity measures in order to calculate the similarity  between two concepts in the UMLS. The measures use the UMLS-Interface  module to access the UMLS present in a mysql database.",' >> META_new.json
	$(NOECHO) $(ECHO) '   "author" : [' >> META_new.json
	$(NOECHO) $(ECHO) '      "Bridget McInnes <bthomson@cs.umn.edu>, Ted Pedersen <tpederse@d.umn.edu>, Serguei Pakhomov <pakh0002 at umn.edu>, Ying Liu <liux0395@umn.edu>"' >> META_new.json
	$(NOECHO) $(ECHO) '   ],' >> META_new.json
	$(NOECHO) $(ECHO) '   "dynamic_config" : 1,' >> META_new.json
	$(NOECHO) $(ECHO) '   "generated_by" : "ExtUtils::MakeMaker version 7.1002, CPAN::Meta::Converter version 2.150005",' >> META_new.json
	$(NOECHO) $(ECHO) '   "license" : [' >> META_new.json
	$(NOECHO) $(ECHO) '      "unknown"' >> META_new.json
	$(NOECHO) $(ECHO) '   ],' >> META_new.json
	$(NOECHO) $(ECHO) '   "meta-spec" : {' >> META_new.json
	$(NOECHO) $(ECHO) '      "url" : "http://search.cpan.org/perldoc?CPAN::Meta::Spec",' >> META_new.json
	$(NOECHO) $(ECHO) '      "version" : "2"' >> META_new.json
	$(NOECHO) $(ECHO) '   },' >> META_new.json
	$(NOECHO) $(ECHO) '   "name" : "UMLS-Similarity",' >> META_new.json
	$(NOECHO) $(ECHO) '   "no_index" : {' >> META_new.json
	$(NOECHO) $(ECHO) '      "directory" : [' >> META_new.json
	$(NOECHO) $(ECHO) '         "t",' >> META_new.json
	$(NOECHO) $(ECHO) '         "inc"' >> META_new.json
	$(NOECHO) $(ECHO) '      ]' >> META_new.json
	$(NOECHO) $(ECHO) '   },' >> META_new.json
	$(NOECHO) $(ECHO) '   "prereqs" : {' >> META_new.json
	$(NOECHO) $(ECHO) '      "build" : {' >> META_new.json
	$(NOECHO) $(ECHO) '         "requires" : {' >> META_new.json
	$(NOECHO) $(ECHO) '            "ExtUtils::MakeMaker" : "0"' >> META_new.json
	$(NOECHO) $(ECHO) '         }' >> META_new.json
	$(NOECHO) $(ECHO) '      },' >> META_new.json
	$(NOECHO) $(ECHO) '      "configure" : {' >> META_new.json
	$(NOECHO) $(ECHO) '         "requires" : {' >> META_new.json
	$(NOECHO) $(ECHO) '            "ExtUtils::MakeMaker" : "0"' >> META_new.json
	$(NOECHO) $(ECHO) '         }' >> META_new.json
	$(NOECHO) $(ECHO) '      },' >> META_new.json
	$(NOECHO) $(ECHO) '      "runtime" : {' >> META_new.json
	$(NOECHO) $(ECHO) '         "requires" : {' >> META_new.json
	$(NOECHO) $(ECHO) '            "File::Path" : "2.08",' >> META_new.json
	$(NOECHO) $(ECHO) '            "File::Spec" : "3.31",' >> META_new.json
	$(NOECHO) $(ECHO) '            "Lingua::Stem" : "0.84",' >> META_new.json
	$(NOECHO) $(ECHO) '            "Text::NSP" : "1.17",' >> META_new.json
	$(NOECHO) $(ECHO) '            "Text::OverlapFinder" : "0.02",' >> META_new.json
	$(NOECHO) $(ECHO) '            "UMLS::Interface" : "1.43"' >> META_new.json
	$(NOECHO) $(ECHO) '         }' >> META_new.json
	$(NOECHO) $(ECHO) '      }' >> META_new.json
	$(NOECHO) $(ECHO) '   },' >> META_new.json
	$(NOECHO) $(ECHO) '   "release_status" : "stable",' >> META_new.json
	$(NOECHO) $(ECHO) '   "version" : "1.47",' >> META_new.json
	$(NOECHO) $(ECHO) '   "x_serialization_backend" : "JSON::PP version 2.27300_01"' >> META_new.json
	$(NOECHO) $(ECHO) '}' >> META_new.json
	-$(NOECHO) $(MV) META_new.json $(DISTVNAME)/META.json


# --- MakeMaker signature section:
signature :
	cpansign -s


# --- MakeMaker dist_basics section:
distclean :: realclean distcheck
	$(NOECHO) $(NOOP)

distcheck :
	$(PERLRUN) "-MExtUtils::Manifest=fullcheck" -e fullcheck

skipcheck :
	$(PERLRUN) "-MExtUtils::Manifest=skipcheck" -e skipcheck

manifest :
	$(PERLRUN) "-MExtUtils::Manifest=mkmanifest" -e mkmanifest

veryclean : realclean
	$(RM_F) *~ */*~ *.orig */*.orig *.bak */*.bak *.old */*.old



# --- MakeMaker dist_core section:

dist : $(DIST_DEFAULT) $(FIRST_MAKEFILE)
	$(NOECHO) $(ABSPERLRUN) -l -e 'print '\''Warning: Makefile possibly out of date with $(VERSION_FROM)'\''' \
	  -e '    if -e '\''$(VERSION_FROM)'\'' and -M '\''$(VERSION_FROM)'\'' < -M '\''$(FIRST_MAKEFILE)'\'';' --

tardist : $(DISTVNAME).tar$(SUFFIX)
	$(NOECHO) $(NOOP)

uutardist : $(DISTVNAME).tar$(SUFFIX)
	uuencode $(DISTVNAME).tar$(SUFFIX) $(DISTVNAME).tar$(SUFFIX) > $(DISTVNAME).tar$(SUFFIX)_uu
	$(NOECHO) $(ECHO) 'Created $(DISTVNAME).tar$(SUFFIX)_uu'

$(DISTVNAME).tar$(SUFFIX) : distdir
	$(PREOP)
	$(TO_UNIX)
	$(TAR) $(TARFLAGS) $(DISTVNAME).tar $(DISTVNAME)
	$(RM_RF) $(DISTVNAME)
	$(COMPRESS) $(DISTVNAME).tar
	$(NOECHO) $(ECHO) 'Created $(DISTVNAME).tar$(SUFFIX)'
	$(POSTOP)

zipdist : $(DISTVNAME).zip
	$(NOECHO) $(NOOP)

$(DISTVNAME).zip : distdir
	$(PREOP)
	$(ZIP) $(ZIPFLAGS) $(DISTVNAME).zip $(DISTVNAME)
	$(RM_RF) $(DISTVNAME)
	$(NOECHO) $(ECHO) 'Created $(DISTVNAME).zip'
	$(POSTOP)

shdist : distdir
	$(PREOP)
	$(SHAR) $(DISTVNAME) > $(DISTVNAME).shar
	$(RM_RF) $(DISTVNAME)
	$(NOECHO) $(ECHO) 'Created $(DISTVNAME).shar'
	$(POSTOP)


# --- MakeMaker distdir section:
create_distdir :
	$(RM_RF) $(DISTVNAME)
	$(PERLRUN) "-MExtUtils::Manifest=manicopy,maniread" \
		-e "manicopy(maniread(),'$(DISTVNAME)', '$(DIST_CP)');"

distdir : create_distdir distmeta 
	$(NOECHO) $(NOOP)



# --- MakeMaker dist_test section:
disttest : distdir
	cd $(DISTVNAME) && $(ABSPERLRUN) Makefile.PL 
	cd $(DISTVNAME) && $(MAKE) $(PASTHRU)
	cd $(DISTVNAME) && $(MAKE) test $(PASTHRU)



# --- MakeMaker dist_ci section:
ci :
	$(ABSPERLRUN) -MExtUtils::Manifest=maniread -e '@all = sort keys %{ maniread() };' \
	  -e 'print(qq{Executing $(CI) @all\n});' \
	  -e 'system(qq{$(CI) @all}) == 0 or die $$!;' \
	  -e 'print(qq{Executing $(RCS_LABEL) ...\n});' \
	  -e 'system(qq{$(RCS_LABEL) @all}) == 0 or die $$!;' --


# --- MakeMaker distmeta section:
distmeta : create_distdir metafile
	$(NOECHO) cd $(DISTVNAME) && $(ABSPERLRUN) -MExtUtils::Manifest=maniadd -e 'exit unless -e q{META.yml};' \
	  -e 'eval { maniadd({q{META.yml} => q{Module YAML meta-data (added by MakeMaker)}}) }' \
	  -e '    or print "Could not add META.yml to MANIFEST: $$$${'\''@'\''}\n"' --
	$(NOECHO) cd $(DISTVNAME) && $(ABSPERLRUN) -MExtUtils::Manifest=maniadd -e 'exit unless -f q{META.json};' \
	  -e 'eval { maniadd({q{META.json} => q{Module JSON meta-data (added by MakeMaker)}}) }' \
	  -e '    or print "Could not add META.json to MANIFEST: $$$${'\''@'\''}\n"' --



# --- MakeMaker distsignature section:
distsignature : distmeta
	$(NOECHO) cd $(DISTVNAME) && $(ABSPERLRUN) -MExtUtils::Manifest=maniadd -e 'eval { maniadd({q{SIGNATURE} => q{Public-key signature (added by MakeMaker)}}) }' \
	  -e '    or print "Could not add SIGNATURE to MANIFEST: $$$${'\''@'\''}\n"' --
	$(NOECHO) cd $(DISTVNAME) && $(TOUCH) SIGNATURE
	cd $(DISTVNAME) && cpansign -s



# --- MakeMaker install section:

install :: pure_install doc_install
	$(NOECHO) $(NOOP)

install_perl :: pure_perl_install doc_perl_install
	$(NOECHO) $(NOOP)

install_site :: pure_site_install doc_site_install
	$(NOECHO) $(NOOP)

install_vendor :: pure_vendor_install doc_vendor_install
	$(NOECHO) $(NOOP)

pure_install :: pure_$(INSTALLDIRS)_install
	$(NOECHO) $(NOOP)

doc_install :: doc_$(INSTALLDIRS)_install
	$(NOECHO) $(NOOP)

pure__install : pure_site_install
	$(NOECHO) $(ECHO) INSTALLDIRS not defined, defaulting to INSTALLDIRS=site

doc__install : doc_site_install
	$(NOECHO) $(ECHO) INSTALLDIRS not defined, defaulting to INSTALLDIRS=site

pure_perl_install :: all
	$(NOECHO) $(MOD_INSTALL) \
		read "$(PERL_ARCHLIB)/auto/$(FULLEXT)/.packlist" \
		write "$(DESTINSTALLARCHLIB)/auto/$(FULLEXT)/.packlist" \
		"$(INST_LIB)" "$(DESTINSTALLPRIVLIB)" \
		"$(INST_ARCHLIB)" "$(DESTINSTALLARCHLIB)" \
		"$(INST_BIN)" "$(DESTINSTALLBIN)" \
		"$(INST_SCRIPT)" "$(DESTINSTALLSCRIPT)" \
		"$(INST_MAN1DIR)" "$(DESTINSTALLMAN1DIR)" \
		"$(INST_MAN3DIR)" "$(DESTINSTALLMAN3DIR)"
	$(NOECHO) $(WARN_IF_OLD_PACKLIST) \
		"$(SITEARCHEXP)/auto/$(FULLEXT)"


pure_site_install :: all
	$(NOECHO) $(MOD_INSTALL) \
		read "$(SITEARCHEXP)/auto/$(FULLEXT)/.packlist" \
		write "$(DESTINSTALLSITEARCH)/auto/$(FULLEXT)/.packlist" \
		"$(INST_LIB)" "$(DESTINSTALLSITELIB)" \
		"$(INST_ARCHLIB)" "$(DESTINSTALLSITEARCH)" \
		"$(INST_BIN)" "$(DESTINSTALLSITEBIN)" \
		"$(INST_SCRIPT)" "$(DESTINSTALLSITESCRIPT)" \
		"$(INST_MAN1DIR)" "$(DESTINSTALLSITEMAN1DIR)" \
		"$(INST_MAN3DIR)" "$(DESTINSTALLSITEMAN3DIR)"
	$(NOECHO) $(WARN_IF_OLD_PACKLIST) \
		"$(PERL_ARCHLIB)/auto/$(FULLEXT)"

pure_vendor_install :: all
	$(NOECHO) $(MOD_INSTALL) \
		read "$(VENDORARCHEXP)/auto/$(FULLEXT)/.packlist" \
		write "$(DESTINSTALLVENDORARCH)/auto/$(FULLEXT)/.packlist" \
		"$(INST_LIB)" "$(DESTINSTALLVENDORLIB)" \
		"$(INST_ARCHLIB)" "$(DESTINSTALLVENDORARCH)" \
		"$(INST_BIN)" "$(DESTINSTALLVENDORBIN)" \
		"$(INST_SCRIPT)" "$(DESTINSTALLVENDORSCRIPT)" \
		"$(INST_MAN1DIR)" "$(DESTINSTALLVENDORMAN1DIR)" \
		"$(INST_MAN3DIR)" "$(DESTINSTALLVENDORMAN3DIR)"


doc_perl_install :: all
	$(NOECHO) $(ECHO) Appending installation info to "$(DESTINSTALLARCHLIB)/perllocal.pod"
	-$(NOECHO) $(MKPATH) "$(DESTINSTALLARCHLIB)"
	-$(NOECHO) $(DOC_INSTALL) \
		"Module" "$(NAME)" \
		"installed into" $(INSTALLPRIVLIB) \
		LINKTYPE "$(LINKTYPE)" \
		VERSION "$(VERSION)" \
		EXE_FILES "$(EXE_FILES)" \
		>> "$(DESTINSTALLARCHLIB)/perllocal.pod"

doc_site_install :: all
	$(NOECHO) $(ECHO) Appending installation info to "$(DESTINSTALLARCHLIB)/perllocal.pod"
	-$(NOECHO) $(MKPATH) "$(DESTINSTALLARCHLIB)"
	-$(NOECHO) $(DOC_INSTALL) \
		"Module" "$(NAME)" \
		"installed into" $(INSTALLSITELIB) \
		LINKTYPE "$(LINKTYPE)" \
		VERSION "$(VERSION)" \
		EXE_FILES "$(EXE_FILES)" \
		>> "$(DESTINSTALLARCHLIB)/perllocal.pod"

doc_vendor_install :: all
	$(NOECHO) $(ECHO) Appending installation info to "$(DESTINSTALLARCHLIB)/perllocal.pod"
	-$(NOECHO) $(MKPATH) "$(DESTINSTALLARCHLIB)"
	-$(NOECHO) $(DOC_INSTALL) \
		"Module" "$(NAME)" \
		"installed into" $(INSTALLVENDORLIB) \
		LINKTYPE "$(LINKTYPE)" \
		VERSION "$(VERSION)" \
		EXE_FILES "$(EXE_FILES)" \
		>> "$(DESTINSTALLARCHLIB)/perllocal.pod"


uninstall :: uninstall_from_$(INSTALLDIRS)dirs
	$(NOECHO) $(NOOP)

uninstall_from_perldirs ::
	$(NOECHO) $(UNINSTALL) "$(PERL_ARCHLIB)/auto/$(FULLEXT)/.packlist"

uninstall_from_sitedirs ::
	$(NOECHO) $(UNINSTALL) "$(SITEARCHEXP)/auto/$(FULLEXT)/.packlist"

uninstall_from_vendordirs ::
	$(NOECHO) $(UNINSTALL) "$(VENDORARCHEXP)/auto/$(FULLEXT)/.packlist"


# --- MakeMaker force section:
# Phony target to force checking subdirectories.
FORCE :
	$(NOECHO) $(NOOP)


# --- MakeMaker perldepend section:


# --- MakeMaker makefile section:
# We take a very conservative approach here, but it's worth it.
# We move Makefile to Makefile.old here to avoid gnu make looping.
$(FIRST_MAKEFILE) : Makefile.PL $(CONFIGDEP)
	$(NOECHO) $(ECHO) "Makefile out-of-date with respect to $?"
	$(NOECHO) $(ECHO) "Cleaning current config before rebuilding Makefile..."
	-$(NOECHO) $(RM_F) $(MAKEFILE_OLD)
	-$(NOECHO) $(MV)   $(FIRST_MAKEFILE) $(MAKEFILE_OLD)
	- $(MAKE) $(USEMAKEFILE) $(MAKEFILE_OLD) clean $(DEV_NULL)
	$(PERLRUN) Makefile.PL 
	$(NOECHO) $(ECHO) "==> Your Makefile has been rebuilt. <=="
	$(NOECHO) $(ECHO) "==> Please rerun the $(MAKE) command.  <=="
	$(FALSE)



# --- MakeMaker staticmake section:

# --- MakeMaker makeaperl section ---
MAP_TARGET    = perl
FULLPERL      = "/usr/bin/perl.exe"

$(MAP_TARGET) :: static $(MAKE_APERL_FILE)
	$(MAKE) $(USEMAKEFILE) $(MAKE_APERL_FILE) $@

$(MAKE_APERL_FILE) : $(FIRST_MAKEFILE) pm_to_blib
	$(NOECHO) $(ECHO) Writing \"$(MAKE_APERL_FILE)\" for this $(MAP_TARGET)
	$(NOECHO) $(PERLRUNINST) \
		Makefile.PL DIR="" \
		MAKEFILE=$(MAKE_APERL_FILE) LINKTYPE=static \
		MAKEAPERL=1 NORECURS=1 CCCDLFLAGS=


# --- MakeMaker test section:

TEST_VERBOSE=0
TEST_TYPE=test_$(LINKTYPE)
TEST_FILE = test.pl
TEST_FILES = t/*.t
TESTDB_SW = -d

testdb :: testdb_$(LINKTYPE)

test :: $(TEST_TYPE) subdirs-test

subdirs-test ::
	$(NOECHO) $(NOOP)


test_dynamic :: pure_all
	PERL_DL_NONLAZY=1 $(FULLPERLRUN) "-MExtUtils::Command::MM" "-MTest::Harness" "-e" "undef *Test::Harness::Switches; test_harness($(TEST_VERBOSE), '$(INST_LIB)', '$(INST_ARCHLIB)')" $(TEST_FILES)

testdb_dynamic :: pure_all
	PERL_DL_NONLAZY=1 $(FULLPERLRUN) $(TESTDB_SW) "-I$(INST_LIB)" "-I$(INST_ARCHLIB)" $(TEST_FILE)

test_ : test_dynamic

test_static :: test_dynamic
testdb_static :: testdb_dynamic


# --- MakeMaker ppd section:
# Creates a PPD (Perl Package Description) for a binary distribution.
ppd :
	$(NOECHO) $(ECHO) '<SOFTPKG NAME="$(DISTNAME)" VERSION="$(VERSION)">' > $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '    <ABSTRACT>A suite of Perl modules that implement a number  of semantic similarity measures in order to calculate the similarity  between two concepts in the UMLS. The measures use the UMLS-Interface  module to access the UMLS present in a mysql database.</ABSTRACT>' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '    <AUTHOR>Bridget McInnes &lt;bthomson@cs.umn.edu&gt;, Ted Pedersen &lt;tpederse@d.umn.edu&gt;, Serguei Pakhomov &lt;pakh0002 at umn.edu&gt;, Ying Liu &lt;liux0395@umn.edu&gt;</AUTHOR>' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '    <IMPLEMENTATION>' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <REQUIRE NAME="File::Path" VERSION="2.08" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <REQUIRE NAME="File::Spec" VERSION="3.31" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <REQUIRE NAME="Lingua::Stem" VERSION="0.84" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <REQUIRE NAME="Text::NSP" VERSION="1.17" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <REQUIRE NAME="Text::OverlapFinder" VERSION="0.02" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <REQUIRE NAME="UMLS::Interface" VERSION="1.43" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <ARCHITECTURE NAME="x86_64-msys-thread-multi-5.24" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '        <CODEBASE HREF="" />' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '    </IMPLEMENTATION>' >> $(DISTNAME).ppd
	$(NOECHO) $(ECHO) '</SOFTPKG>' >> $(DISTNAME).ppd


# --- MakeMaker pm_to_blib section:

pm_to_blib : $(FIRST_MAKEFILE) $(TO_INST_PM)
	$(NOECHO) $(ABSPERLRUN) -MExtUtils::Install -e 'pm_to_blib({@ARGV}, '\''$(INST_LIB)/auto'\'', q[$(PM_FILTER)], '\''$(PERM_DIR)'\'')' -- \
	  lib/UMLS/Interface.pm blib/lib/UMLS/Interface.pm \
	  lib/UMLS/Interface/CuiFinder.pm blib/lib/UMLS/Interface/CuiFinder.pm \
	  lib/UMLS/Interface/ErrorHandler.pm blib/lib/UMLS/Interface/ErrorHandler.pm \
	  lib/UMLS/Interface/ICFinder.pm blib/lib/UMLS/Interface/ICFinder.pm \
	  lib/UMLS/Interface/PathFinder.pm blib/lib/UMLS/Interface/PathFinder.pm \
	  lib/UMLS/Interface/STFinder.pm blib/lib/UMLS/Interface/STFinder.pm \
	  lib/UMLS/Similarity.pm blib/lib/UMLS/Similarity.pm \
	  lib/UMLS/Similarity/ErrorHandler.pm blib/lib/UMLS/Similarity/ErrorHandler.pm \
	  lib/UMLS/Similarity/batet.pm blib/lib/UMLS/Similarity/batet.pm \
	  lib/UMLS/Similarity/cdist.pm blib/lib/UMLS/Similarity/cdist.pm \
	  lib/UMLS/Similarity/cmatch.pm blib/lib/UMLS/Similarity/cmatch.pm \
	  lib/UMLS/Similarity/faith.pm blib/lib/UMLS/Similarity/faith.pm \
	  lib/UMLS/Similarity/jcn.pm blib/lib/UMLS/Similarity/jcn.pm \
	  lib/UMLS/Similarity/lch.pm blib/lib/UMLS/Similarity/lch.pm \
	  lib/UMLS/Similarity/lesk.pm blib/lib/UMLS/Similarity/lesk.pm \
	  lib/UMLS/Similarity/lin.pm blib/lib/UMLS/Similarity/lin.pm \
	  lib/UMLS/Similarity/nam.pm blib/lib/UMLS/Similarity/nam.pm \
	  lib/UMLS/Similarity/path.pm blib/lib/UMLS/Similarity/path.pm \
	  lib/UMLS/Similarity/pks.pm blib/lib/UMLS/Similarity/pks.pm \
	  lib/UMLS/Similarity/random.pm blib/lib/UMLS/Similarity/random.pm \
	  lib/UMLS/Similarity/res.pm blib/lib/UMLS/Similarity/res.pm \
	  lib/UMLS/Similarity/sanchez.pm blib/lib/UMLS/Similarity/sanchez.pm \
	  lib/UMLS/Similarity/upath.pm blib/lib/UMLS/Similarity/upath.pm \
	  lib/UMLS/Similarity/vector.pm blib/lib/UMLS/Similarity/vector.pm \
	  lib/UMLS/Similarity/wup.pm blib/lib/UMLS/Similarity/wup.pm \
	  lib/UMLS/Similarity/zhong.pm blib/lib/UMLS/Similarity/zhong.pm \
	  lib/UMLS/icfrequency.default.dat blib/lib/UMLS/icfrequency.default.dat \
	  lib/UMLS/vector-index.dat blib/lib/UMLS/vector-index.dat \
	  lib/UMLS/vector-matrix.dat blib/lib/UMLS/vector-matrix.dat 
	$(NOECHO) $(TOUCH) pm_to_blib


# --- MakeMaker selfdocument section:


# --- MakeMaker postamble section:


# End.
