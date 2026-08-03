# Default ProGuard/R8 rules for The Trace Protocol.
#
# Room, Compose and Lifecycle all ship consumer rules inside their AARs, so the
# release build works without extra configuration. The keeps below are defensive:
# they protect the Room-generated implementation classes and the entity used for
# reflective column binding.

-keep class com.trace.app.TraceEntity { *; }
-keep class com.trace.app.AppDatabase_Impl { *; }
-keep class * extends androidx.room.RoomDatabase { <init>(); }

# Keep line numbers for readable crash reports (stripped of source file names).
-keepattributes SourceFile,LineNumberTable
-renamesourcefileattribute SourceFile
