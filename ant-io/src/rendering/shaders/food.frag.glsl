#version 300 es
// =============================================================================
// ANCHOR:SHADER_FOOD_FRAG
// Description: Renders each food chunk as a smooth soft circle.
//              Discards fragments outside radius. Soft edge via smoothstep.
// Uniforms:
//   u_food_color - base food color (e.g. bright green/yellow)
// =============================================================================

precision mediump float;

in vec2 v_local;

uniform vec3 u_food_color;

out vec4 fragColor;

void main() {
    float dist = length(v_local);
    if (dist > 1.0) discard;

    // Soft edge + slight specular highlight at center
    float edge     = smoothstep(1.0, 0.75, dist);
    float highlight = smoothstep(0.3, 0.0, dist) * 0.4;

    vec3 col = u_food_color + vec3(highlight);
    fragColor = vec4(clamp(col, 0.0, 1.0), edge);
}
