<?php

/*
 * The MIT License
 *
 * Copyright 2014 Anders Bo Rasmussen
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Fluxuser\Utils;

/**
 * Description of PivotalTracker
 *
 * @author Anders Bo Rasmussen
 */
class PivotalTracker {

    const ACCEPTED = 'accepted';
    const DELIVERED = 'delivered';
    const FINISHED = 'finished';
    const STARTED = 'started';
    const REJECTED = 'rejected';
    const PLANNED = 'planned';
    const UNSTARTED = 'unstarted';
    const UNSCHEDULED = 'unscheduled';    
    
    private $token;
    private $error;

    /**
     * Retrieve all projects from the current user.
     * 
     * @return array of projects (JSON)
     */
    public function getProjects() {
        $ch = curl_init('https://www.pivotaltracker.com/services/v5/projects');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Retrieve the specific project with the specified id.
     * 
     * @param int $id
     * @return project (JSON)
     */
    public function getProject($id) {
        $ch = curl_init('https://www.pivotaltracker.com/services/v5/projects/' . $id);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Retrieve all labels in a given project.
     * 
     * @param int $projectId
     * @return array of labels (JSON)
     */
    public function getProjectLabels($projectId) {
        $ch = curl_init('https://www.pivotaltracker.com/services/v5/projects/' . $projectId . '/labels');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Retrieve a given label in a given project.
     * 
     * @param int $projectId
     * @param int $labelId
     * @return label (JSON)
     */
    public function getProjectLabel($projectId, $labelId) {
        $ch = curl_init('https://www.pivotaltracker.com/services/v5/projects/' . $projectId . '/labels/' . $labelId);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * 
     * @param int $projectId
     * @param int $storyId
     * @return array of labels (JSON)
     */
    public function getProjectStoryLabels($projectId, $storyId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId/labels");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Fetch all stories for the specified project.
     * 
     * @param int $projectId
     * @param array $filter
     * @return array of stories (JSON)
     */
    public function getProjectStories($projectId, $filter = ['limit' => 1000000]) {
        $params = '';
        foreach ($filter as $key => $value) {
            $params = "?$key=$value";
        }
        $url = "https://www.pivotaltracker.com/services/v5/projects/$projectId/stories$params";
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the specific story.
     * 
     * @param int $projectId
     * @param int $storyId
     * @return story (JSON)
     */
    public function getProjectStory($projectId, $storyId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the owners of a specific story.
     * 
     * @param int $projectId
     * @param int $storyId
     * @return array of owners (JSON)
     */
    public function getProjectStoryOwners($projectId, $storyId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId/owners");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the tasks of a specified story.
     * 
     * @param int $projectId
     * @param int $storyId
     * @return array of tasks (JSON)
     */
    public function getProjectStoryTasks($projectId, $storyId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId/tasks");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns specified tasks of a specified story.
     * 
     * @param int $projectId
     * @param int $storyId
     * @param int $taskId
     * @return task (JSON)
     */
    public function getProjectStoryTask($projectId, $storyId, $taskId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId/tasks/$taskId");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns a list of all epics in the projects
     * 
     * @param int $projectId
     * @return array of epics (JSON)
     */
    public function getProjectEpics($projectId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/epics");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the specified Epic
     * 
     * @param int $projectId
     * @param int $epicId
     * @return epic (JSON)
     */
    public function getProjectEpic($projectId, $epicId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/epics/$epicId");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the specified story's comments.
     * 
     * @param int $projectId
     * @param int $storyId
     * @return array of comments (JSON)
     */
    public function getProjectStoryComments($projectId, $storyId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId/comments");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the specified comment.
     * 
     * @param int $projectId
     * @param int $storyId
     * @param int $commentId
     * @return comment (JSON)
     */
    public function getProjectStoryComment($projectId, $storyId, $commentId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId/comments/$commentId");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the specified epic's comments.
     * 
     * @param int $projectId
     * @param int $epicId
     * @return array of comments (JSON)
     */
    public function getProjectEpicComments($projectId, $epicId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/epics/$epicId/comments");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Returns the specified comment.
     * 
     * @param int $projectId
     * @param int $epicId
     * @param int $commentId
     * @return array of comments (JSON)
     */
    public function getProjectEpicComment($projectId, $epicId, $commentId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/epics/$epicId/comments/$commentId");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Search for stories and epics.
     * 
     * @param int $projectId
     * @param string $query
     * @return array of stories and epics (JSON)
     */
    public function searchForStoriesAndEpicsInProject($projectId, $query) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/search?query=$query");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * List all of the memberships in a project
     * 
     * @param int $projectId
     * @return array of project members (JSON)
     */
    public function getProjectMemberships($projectId) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/memberships");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }

    /**
     * Update's the specified story's state
     * 
     * @param int $projectId
     * @param int $storyId
     * @param string $state
     */
    public function setProjectStoryState($projectId, $storyId, $state) {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/projects/$projectId/stories/$storyId?current_state=$state");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_PUT => true,
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Returns information from the user's profile plus the list of projects 
     * to which the user has access.
     * 
     * @return stdClass (JSON)
     */
    public function getUserProfile() {
        $ch = curl_init("https://www.pivotaltracker.com/services/v5/me");
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTPHEADER => array("X-TrackerToken: $this->token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        curl_close($ch);
        return $json;
    }
    
    /**
     * 
     * @param type $username
     * @param type $password
     */
    public function getToken($username, $password) {
        $ch = curl_init('https://www.pivotaltracker.com/services/v5/me');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $json = json_decode(curl_exec($ch));
        if (curl_error($ch)) {
            $this->error = curl_error($ch);
            return false;
        }
        curl_close($ch);
        $this->setToken($json->api_token);
        return $json->api_token;
    }

    /**
     * Sets the API token used by the Pivotal Tracker API.
     * 
     * @param string $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

    public function getError() {
        return $this->error;
    }

}
