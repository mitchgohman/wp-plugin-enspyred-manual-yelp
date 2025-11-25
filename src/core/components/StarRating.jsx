import React from 'react';
import styled from 'styled-components';

const StarsContainer = styled.div`
    display: flex;
    align-items: center;
    gap: 2px;
`;

const Star = styled.span`
    color: ${props => props.$filled ? '#f15c00' : '#e6e6e6'};
    font-size: ${props => props.$size === 'large' ? '20px' : '16px'};
`;

const StarRating = ({ rating, size = 'small' }) => {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;

    return (
        <StarsContainer>
            {[1, 2, 3, 4, 5].map((star) => {
                if (star <= fullStars) {
                    return <Star key={star} $filled $size={size}>★</Star>;
                } else if (star === fullStars + 1 && hasHalfStar) {
                    return <Star key={star} $filled $size={size}>★</Star>;
                } else {
                    return <Star key={star} $size={size}>★</Star>;
                }
            })}
        </StarsContainer>
    );
};

export default StarRating;
