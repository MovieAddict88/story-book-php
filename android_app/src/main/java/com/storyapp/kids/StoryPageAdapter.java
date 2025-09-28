package com.storyapp.kids;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.viewpager2.adapter.FragmentStateAdapter;

import com.storyapp.kids.models.StoryPage;

import java.util.List;

public class StoryPageAdapter extends FragmentStateAdapter {

    private List<StoryPage> storyPages;

    public StoryPageAdapter(@NonNull FragmentActivity fragmentActivity, List<StoryPage> storyPages) {
        super(fragmentActivity);
        this.storyPages = storyPages;
    }

    @NonNull
    @Override
    public Fragment createFragment(int position) {
        // Create a new fragment instance for each page
        StoryPage page = storyPages.get(position);
        return StoryPageFragment.newInstance(page.getImage(), page.getText());
    }

    @Override
    public int getItemCount() {
        return storyPages.size();
    }
}