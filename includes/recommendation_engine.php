<?php
/**
 * Smart Futsal Recommendation Engine
 * Provides intelligent futsal recommendations based on various factors
 */

class FutsalRecommendationEngine {
    private $futsals;
    private $userPreferences;
    
    public function __construct() {
        $this->futsals = include __DIR__ . '/../handlers/futsals_data.php';
        $this->userPreferences = $this->getDefaultPreferences();
    }
    
    /**
     * Get personalized recommendations for a user
     */
    public function getRecommendations($userId = null, $limit = 6) {
        $recommendations = [];
        
        // Get user-specific preferences if user is logged in
        if ($userId) {
            $this->userPreferences = $this->getUserPreferences($userId);
        }
        
        // Calculate recommendation scores for each futsal
        foreach ($this->futsals as $futsal) {
            // Only recommend futsals with rating > 3.5 AND price < 1500
            if ($futsal['rating'] > 3.5 && $futsal['price'] < 1500) {
                $score = $this->calculateRecommendationScore($futsal);
                $recommendations[] = array_merge($futsal, ['recommendation_score' => $score]);
            }
        }
        
        // Sort by recommendation score (highest first)
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });
        
        // Return top recommendations
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Get recommendations by location preference
     */
    public function getRecommendationsByLocation($location, $limit = 6) {
        $recommendations = [];
        
        foreach ($this->futsals as $futsal) {
            $locationScore = $this->calculateLocationScore($futsal, $location);
            $qualityScore = $this->calculateQualityScore($futsal);
            $totalScore = ($locationScore * 0.7) + ($qualityScore * 0.3);
            
            $recommendations[] = array_merge($futsal, ['recommendation_score' => $totalScore]);
        }
        
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Get recommendations by price range
     */
    public function getRecommendationsByPrice($minPrice, $maxPrice, $limit = 6) {
        $recommendations = [];
        
        foreach ($this->futsals as $futsal) {
            if ($futsal['price'] >= $minPrice && $futsal['price'] <= $maxPrice) {
                $score = $this->calculateRecommendationScore($futsal);
                $recommendations[] = array_merge($futsal, ['recommendation_score' => $score]);
            }
        }
        
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Get top-rated futsals
     */
    public function getTopRatedFutsals($limit = 6) {
        $recommendations = [];
        
        foreach ($this->futsals as $futsal) {
            // Only include futsals with rating > 3.5 AND price < 1500
            if ($futsal['rating'] > 3.5 && $futsal['price'] < 1500) {
                $score = $this->calculateQualityScore($futsal);
                $recommendations[] = array_merge($futsal, ['recommendation_score' => $score]);
            }
        }
        
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Get budget-friendly recommendations
     */
    public function getBudgetFriendly($limit = 6) {
        $recommendations = [];
        
        foreach ($this->futsals as $futsal) {
            // Only include futsals with rating > 3.5 AND price < 1500
            if ($futsal['rating'] > 3.5 && $futsal['price'] < 1500) {
                $score = $this->calculateRecommendationScore($futsal);
                $recommendations[] = array_merge($futsal, ['recommendation_score' => $score]);
            }
        }
        
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Calculate overall recommendation score for a futsal
     */
    private function calculateRecommendationScore($futsal) {
        $score = 0;
        
        // Rating factor (40% weight)
        $ratingScore = ($futsal['rating'] / 5.0) * 40;
        $score += $ratingScore;
        
        // Price factor (20% weight) - lower price gets higher score
        $maxPrice = 1600;
        $priceScore = ((1 - ($futsal['price'] / $maxPrice)) * 20);
        $score += $priceScore;
        
        // Popularity factor (20% weight) - based on rating
        $popularityScore = ($futsal['rating'] / 5.0) * 20;
        $score += $popularityScore;
        
        // Quality factor (20% weight)
        $qualityScore = $this->calculateQualityScore($futsal) * 0.2;
        $score += $qualityScore;
        
        return round($score, 2);
    }
    
    /**
     * Calculate quality score based on rating
     */
    private function calculateQualityScore($futsal) {
        return ($futsal['rating'] / 5.0) * 100;
    }
    
    /**
     * Calculate location-based score
     */
    private function calculateLocationScore($futsal, $preferredLocation) {
        // Simple location matching - can be enhanced with actual distance calculation
        $futsalLocation = strtolower($futsal['address']);
        $preferredLocation = strtolower($preferredLocation);
        
        if (strpos($futsalLocation, $preferredLocation) !== false) {
            return 100; // Perfect match
        }
        
        // Nearby locations (can be expanded)
        $nearbyLocations = [
            'baneshwor' => ['new baneshwor', 'baneswor'],
            'pulchowk' => ['sanepa', 'kupondole'],
            'patan' => ['jawalakhel', 'lagankhel'],
            // Add more location mappings
        ];
        
        foreach ($nearbyLocations as $main => $nearby) {
            if (in_array($preferredLocation, $nearby) && in_array($futsalLocation, $nearby)) {
                return 80; // Nearby match
            }
        }
        
        return 50; // Default score
    }
    
    /**
     * Get default user preferences
     */
    private function getDefaultPreferences() {
        return [
            'preferred_location' => null,
            'price_range' => ['min' => 0, 'max' => 2000],
            'preferred_rating' => 3.5,
            'priority_factors' => ['rating' => 0.4, 'price' => 0.3, 'location' => 0.3]
        ];
    }
    
    /**
     * Get user preferences from database (placeholder)
     */
    private function getUserPreferences($userId) {
        // This would typically fetch from database
        // For now, return default preferences
        return $this->getDefaultPreferences();
    }
    
    /**
     * Save user preferences (placeholder)
     */
    public function saveUserPreferences($userId, $preferences) {
        // This would typically save to database
        return true;
    }
    
    /**
     * Get recommendation explanation
     */
    public function getRecommendationExplanation($futsal, $score) {
        $reasons = [];
        
        if ($futsal['rating'] >= 4.0) {
            $reasons[] = "High rating ({$futsal['rating']}/5.0)";
        }
        
        if ($futsal['price'] <= 1200) {
            $reasons[] = "Budget-friendly (Rs. {$futsal['price']}/hour)";
        }
        
        if ($score >= 80) {
            $reasons[] = "Excellent match for your preferences";
        }
        
        return empty($reasons) ? "Good choice based on overall quality" : implode(" | ", $reasons);
    }
}

// Helper function to get recommendations
function getFutsalRecommendations($type = 'general', $params = [], $limit = 6) {
    $engine = new FutsalRecommendationEngine();
    
    switch ($type) {
        case 'location':
            return $engine->getRecommendationsByLocation($params['location'] ?? '', $limit);
        case 'price':
            return $engine->getRecommendationsByPrice(
                $params['min_price'] ?? 0, 
                $params['max_price'] ?? 2000, 
                $limit
            );
        case 'top_rated':
            return $engine->getTopRatedFutsals($limit);
        case 'budget':
            return $engine->getBudgetFriendly($limit);
        default:
            return $engine->getRecommendations(null, $limit);
    }
}
?>
