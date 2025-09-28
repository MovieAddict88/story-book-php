package com.storyapp.kids.db;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

@Entity(tableName = "offline_stories")
public class OfflineStory {

    @PrimaryKey
    private int id;

    // Store the entire Story object as a JSON string
    private String storyJson;

    // Getters and Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getStoryJson() {
        return storyJson;
    }

    public void setStoryJson(String storyJson) {
        this.storyJson = storyJson;
    }
}