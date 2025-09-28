package com.storyapp.kids.db;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;
import java.util.List;

@Dao
public interface OfflineStoryDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(OfflineStory story);

    @Query("SELECT * FROM offline_stories WHERE id = :storyId")
    OfflineStory getStoryById(int storyId);

    @Query("SELECT * FROM offline_stories")
    List<OfflineStory> getAllStories();

    @Query("DELETE FROM offline_stories WHERE id = :storyId")
    void deleteById(int storyId);
}